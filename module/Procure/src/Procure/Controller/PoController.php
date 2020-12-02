<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Domain\Shared\Constants;
use Application\Domain\Util\JsonErrors;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\AcceptAmendmentCmdHandler;
use Procure\Application\Command\Doctrine\PO\EnableAmendmentCmdHandler;
use Procure\Application\Command\Doctrine\PO\SaveCopyFromQuoteCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\Contracts\PoServiceInterface;
use Procure\Controller\Contracts\ProcureCRUDController;
use Zend\View\Model\ViewModel;

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

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token);

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

            $options = new PostCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
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
        $nmtPlugin = $this->Nmtplugin();

        // POSTING
        // ======================

        $notification = new Notification();

        try {

            $entity_token = $_POST["entity_token"];
            $entity_id = $_POST["entity_id"];
            $version = $_POST["version"];

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token);
            $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&entity_token=%s", $entity_id, $entity_token);

            if ($rootEntity == null) {
                $msg = sprintf("PO #%s is not found!", $entity_id);
                $this->getLogger()->info($msg);
                $this->flashMessenger()->addMessage($redirectUrl);

                $data = array();
                $data['message'] = $msg;
                $data['redirectUrl'] = $redirectUrl;
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($data));
                return $response;
            }

            $options = new UpdateHeaderCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = new EnableAmendmentCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $msg = sprintf("PO %s is enabled for amendment", $entity_id);
            $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $this->getLogger()->info($msg);
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);

            $msg = \sprintf("Error:%s ", $e->getMessage());
            $redirectUrl = sprintf("/procure/po/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
            $notification->addError($msg);
        }
        $this->flashMessenger()->addMessage($msg);
        $data['message'] = $msg;
        $data['redirectUrl'] = $redirectUrl;
        $this->getLogger()->info(json_encode($data));

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=UTF-8');
        $response->setContent(json_encode($data));
        $this->logInfo(\sprintf('Json Last error: %s', JsonErrors::getErrorMessage(json_last_error())));

        return $response;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function createFromQrAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $this->layout("Procure/layout-fullscreen");

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create-from-qr";
        $form_title = "PO from Quote";
        $action = Constants::FORM_ACTION_PO_FROM_QO;
        $viewTemplete = "procure/po/crudHeader";

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
            $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);

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
        $notification = null;
        try {
            $data = $prg;

            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $rootEntity = $this->getProcureService()->createFromQuotation($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromQuoteCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $notification = $cmd->getNotification();
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
                'headerDTO' => $cmd->getOutput(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $redirectUrl = sprintf("/procure/po/view?entity_token=%s&entity_id=%s", $rootEntity->getToken(), $rootEntity->getId());
        $this->flashMessenger()->addMessage($notification->successMessage(true));

        return $this->redirect()->toUrl($redirectUrl);
    }
}
