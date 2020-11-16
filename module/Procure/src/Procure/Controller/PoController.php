<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Domain\Shared\Constants;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\AcceptAmendmentCmdHandler;
use Procure\Application\Command\Doctrine\PO\EnableAmendmentCmdHandler;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Command\PO\SaveCopyFromQuoteCmdHandler;
use Procure\Application\Command\PO\Options\CopyFromQuoteOptions;
use Procure\Application\Command\PO\Options\PoAmendmentEnableOptions;
use Procure\Application\Command\PO\Options\SaveCopyFromQuoteOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Controller\Contracts\ProcureCRUDController;
use Zend\View\Model\ViewModel;
use Procure\Application\Service\Contracts\PoServiceInterface;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoController extends ProcureCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
        $this->setViewTemplate();
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/procure/po';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/po/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/po/dto_list';
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
     */
    public function reviewAmendmentAction()
    {
        $response = $this->getResponse();
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/review-amendment";
        $form_title = "Review Amendment";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/po/review-v1";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');

            $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ======================

        $notification = null;

        try {

            $msg = null;
            $data = $prg;

            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                $msg = sprintf("PO #%s not found", $entity_id);

                $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&token=%s", $entity_id, $entity_token);
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($redirectUrl));
                return $response;
            }

            $options = new PostCmdOptions($rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = new AcceptAmendmentCmdHandler();

            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $this->getLogger()->info(\sprintf("PO amendment #%s accepted by #%s", $entity_id, $this->getUserId()));
            $notification = $cmd->getNotification();
        } catch (\Exception $e) {
            $notification = new Notification();
            $msg = $e->getMessage();
            $notification->addError($msg);
        }

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/po/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        $this->flashMessenger()->addMessage($notification->successMessage(true));
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function enableAmendmentAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();

        // POSTING
        // ======================

        $notification = new Notification();

        try {

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $userId = $u->getId();

            $entity_token = $_POST["entity_token"];
            $entity_id = $_POST["entity_id"];
            $version = $_POST["version"];

            $rootEntity = $this->purchaseOrderService->getPODetailsById($entity_id, $entity_token);
            $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&entity_token=%s", $entity_id, $entity_token);

            if ($rootEntity == null) {
                $msg = sprintf("PO #%s is not found!", $entity_id);
                $this->flashMessenger()->addMessage($redirectUrl);

                $data = array();
                $data['message'] = $msg;
                $data['redirectUrl'] = $redirectUrl;
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($data));
                return $response;
            }

            $options = new PoAmendmentEnableOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $dto = new PoDTO();
            $cmdHandler = new EnableAmendmentCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $msg = sprintf("PO #%s is enabled for amendment", $entity_id);
            $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $this->getLogger()->info($msg);
        } catch (\Exception $e) {
            $msg = \sprintf("Error:%s ", $e->getMessage());
            $redirectUrl = sprintf("/procure/po/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $notification->addError($msg);
        }
        $this->flashMessenger()->addMessage($msg);
        $data = array();
        $data['message'] = $msg;
        $data['redirectUrl'] = $redirectUrl;
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        return $response;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function createFromQrAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/
        /**@var PoDTO $dto ;*/
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create-from-qr";
        $form_title = "PO from Quote";
        $action = Constants::FORM_ACTION_PO_FROM_QO;
        $viewTemplete = "procure/po/crudHeader";

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $source_id = (int) $this->params()->fromQuery('source_id');
            $source_token = $this->params()->fromQuery('source_token');

            /**
             *
             * @var PoServiceInterface $poService ;
             */
            $options = new CopyFromQuoteOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);

            $poService = $this->getProcureService();
            $rootEntity = $poService->createFromQuotation($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $dto = $rootEntity->makeDetailsDTO();

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'source_id' => $source_id,
                'source_token' => $source_token,
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ===============================

        try {
            $data = $prg;

            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $version = $data['version'];

            $rootEntity = $this->getProcureService()->createFromQuotation($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromCmdOptions($this->getCompanyId(), $this->getUserId(), __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromQuoteCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'source_id' => $source_id,
                'source_token' => $source_token,
                'headerDTO' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/po/view?entity_token=%s&entity_id=%s", $dto->getToken(), $dto->getId());
        $this->flashMessenger()->addMessage($notification->successMessage(true));

        return $this->redirect()->toUrl($redirectUrl);
    }
}
