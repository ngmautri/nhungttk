<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Util\Pagination\Paginator;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\PO\AcceptAmendmentCmdHandler;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\Command\PO\CloneAndSavePOCmdHandler;
use Procure\Application\Command\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\PO\EditHeaderCmdHandler;
use Procure\Application\Command\PO\EnableAmendmentCmdHandler;
use Procure\Application\Command\PO\InlineUpdateRowCmdHandler;
use Procure\Application\Command\PO\PostCmdHandler;
use Procure\Application\Command\PO\SaveCopyFromQuoteCmdHandler;
use Procure\Application\Command\PO\UpdateRowCmdHandler;
use Procure\Application\Command\PO\Options\CopyFromQuoteOptions;
use Procure\Application\Command\PO\Options\PoAmendmentAcceptOptions;
use Procure\Application\Command\PO\Options\PoAmendmentEnableOptions;
use Procure\Application\Command\PO\Options\PoCreateOptions;
use Procure\Application\Command\PO\Options\PoPostOptions;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;
use Procure\Application\Command\PO\Options\PoRowUpdateOptions;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\Command\PO\Options\SaveCopyFromQuoteOptions;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Application\Reporting\PO\PoReporter;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Shared\Constants;
use Zend\Escaper\Escaper;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoController extends AbstractGenericController
{

    const BASE_URL = '/procure/po/%s';

    const VIEW_URL = '/procure/po/view?entity_id=%s&entity_token=%s';

    const REVIEW_URL = '/procure/po/review1?entity_id=%s&entity_token=%s';

    const ADD_ROW_URL = '/procure/po/add-row?target_id=%s&target_token=%s';

    protected $poService;

    protected $purchaseOrderService;

    protected $poSearchService;

    protected $poReporter;

    protected $eventBusService;

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

            $options = new CopyFromQuoteOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);
            $rootEntity = $this->getPurchaseOrderService()->createFromQuotation($source_id, $source_token, $options);

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

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();
            $source_id = $data['source_id'];
            $source_token = $data['source_token'];
            $version = $data['version'];

            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $options = new CopyFromQuoteOptions($u->getCompany()->getId(), $u->getId(), __METHOD__);

            $rootEntity = $this->getPurchaseOrderService()->createFromQuotation($source_id, $source_token, $options);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new SaveCopyFromQuoteOptions($companyId, $userId, __METHOD__, $rootEntity);
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

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     *
     * @version 2.6
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create";
        $form_title = "Create PO";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudHeader";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $data = $prg;

            /**
             *
             * @var \Application\Entity\MlaUsers $u ;
             * @var PoDTO $dto ;
             */
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();

            $options = new PoCreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());

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
                'version' => null,
                'headerDTO' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/add-row?target_token=%s&target_id=%s", $dto->getToken(), $dto->getId());
        $this->getLogger()->info(\sprintf("PO #%s is created by #%s", $dto->getId(), $u->getId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @version 2.6
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addRowAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var PORowDTO $dto ;
         */

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/add-row";
        $form_title = "Add PO Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudPORow";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->purchaseOrderService->getPO($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,

                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $target_id,
                'target_token' => $target_token,

                'dto' => null,
                'headerDTO' => $rootEntity->makeHeaderDTO(),
                'docRevisionNo' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // ==============

        try {

            $data = $prg;

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $dto = DTOFactory::createDTOFromArray($data, new PoRowDetailsDTO());
            $userId = $u->getId();

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $version = $data['docRevisionNo'];

            $rootEntity = $this->purchaseOrderService->getPO($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PoRowCreateOptions($rootEntity, $target_id, $target_token, $version, $userId, __METHOD__);
            $cmdHandler = new AddRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
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
                'target_id' => $target_id,
                'target_token' => $target_token,
                'dto' => $dto,
                'headerDTO' => $rootEntity->makeHeaderDTO(),
                'docRevisionNo' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            $this->getLogger()->info(\sprintf("PO Row of %s is not created by #%s. Errors:%s", $target_id, $u->getId(), $notification->errorMessage()));

            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/add-row?target_id=%s&target_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("PO Row of %s is created by #%s", $target_id, $u->getId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @version 2.6
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateRowAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         * @var \Application\Entity\MlaUsers $u ;
         * @var PORowDetailsDTO $dto ;
         */

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/update-row";
        $form_title = "Update PO Row";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/procure/po/crudPORow";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');

            $result = $this->purchaseOrderService->getPOofRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if (! $rootDTO instanceof PoDetailsDTO || ! $localDTO instanceof PORowDetailsDTO) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'docRevisionNo' => $rootDTO->getRevisionNo(),
                'headerDTO' => $rootDTO,
                'dto' => $localDTO, // row
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // Posting
        // =============================

        try {

            $data = $prg;

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $dto = DTOFactory::createDTOFromArray($data, new PORowDetailsDTO());

            $userId = $u->getId();

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['docRevisionNo'];

            $result = $this->purchaseOrderService->getPOofRow($target_id, $target_token, $entity_id, $entity_token);

            $rootEntity = null;
            $localEntity = null;
            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootEntity"])) {
                $rootEntity = $result["rootEntity"];
            }

            if (isset($result["localEntity"])) {
                $localEntity = $result["localEntity"];
            }
            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if ($rootEntity == null || $localEntity == null || $rootDTO == null || $localDTO == null) {
                $this->flashMessenger()->addMessage($redirectUrl);

                return $this->redirect()->toRoute('not_found');
            }

            $options = new PoRowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());

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
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'docRevisionNo' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $dto,
                'headerDTO' => $rootDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/review1?entity_id=%s&entity_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("PO Row of %s is updated by #%s", $target_id, $u->getId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function inlineUpdateRowAction()
    {
        $a_json_final = array();
        $escaper = new Escaper();

        $sent_list = json_decode($_POST['sent_list'], true);
        $to_update = $sent_list['addList'];

        $this->getLogger()->info(\serialize($to_update));
        $response = $this->getResponse();

        try {
            foreach ($to_update as $a) {

                $dto = DTOFactory::createDTOFromArray($a, new PORowDetailsDTO());

                $target_id = $a['docId'];
                $target_token = $a['docToken'];
                $entity_id = $a['id'];
                $entity_token = $a['token'];
                $version = $a['docRevisionNo'];

                $result = $this->purchaseOrderService->getPOofRow($target_id, $target_token, $entity_id, $entity_token);

                $rootEntity = null;
                $localEntity = null;
                $rootDTO = null;
                $localDTO = null;
                $this->getLogger()->info(\serialize($a));

                if (isset($result["rootEntity"])) {
                    $rootEntity = $result["rootEntity"];
                }

                if (isset($result["localEntity"])) {
                    $localEntity = $result["localEntity"];
                }
                if (isset($result["rootDTO"])) {
                    $rootDTO = $result["rootDTO"];
                }

                if (isset($result["localDTO"])) {
                    $localDTO = $result["localDTO"];
                }

                $options = new PoRowUpdateOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
                $cmdHandler = new InlineUpdateRowCmdHandler();
                $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
                $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
                $cmd->execute();

                $notification = $dto->getNotification();
                // $this->getLogger()->info(\serialize($notification));
            }
        } catch (\Exception $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
            $this->getLogger()->error($e->getMessage());
        }

        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] PO Row updated!"));
        return $response;
    }

    /**
     *
     * @version 2.6
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
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
     * @version 2.6
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        /**@var \Application\Entity\MlaUsers $u ;*/

        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/update";
        $form_title = "Edit PO";
        $action = \Procure\Domain\Shared\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "procure/po/crudHeader";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('entity_token');
            $dto = $this->purchaseOrderService->getPOHeaderById($entity_id, $token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $token,
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
        try {

            // POSTING
            $data = $prg;

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());

            $userId = $u->getId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->purchaseOrderService->getPOHeaderById($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PoUpdateOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__, False);

            $cmdHandler = new EditHeaderCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());

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
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $dto,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function cloneAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form_action = "/procure/po/clone";
        $form_title = "Clone PO";
        $action = FormActions::EDIT;
        $viewTemplete = "procure/po/crudHeader";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('entity_token');
            $dto = $this->purchaseOrderService->getPOHeaderById($entity_id, $token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $token,
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
        try {

            // POSTING
            $data = $prg;
            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->purchaseOrderService->getPODetailsById($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PoUpdateOptions($rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__, False);

            $cmdHandler = new CloneAndSavePOCmdHandler();
            $cmdHanderDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'headerDTO' => $dto,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @version 2.6 *
     * @return \Zend\View\Model\ViewModel
     */
    public function review1Action()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/review1";
        $form_title = "Review PO";
        $action = Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/po/review-v1";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $this->layout("Procure/layout-fullscreen");

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
        // ====================================
        $notification = new Notification();
        $msg = null;
        try {

            $data = $prg;

            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());

            $userId = $this->getUserId();
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $redirectUrl = sprintf(self::VIEW_URL, $entity_id, $entity_token);
            $this->getLogger()->info($redirectUrl);

            $rootEntity = $this->purchaseOrderService->getPODetailsById($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PoPostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();

            $notification = $dto->getNotification();
            $msg = sprintf("PO #%s is posted", $entity_id);
            $redirectUrl = sprintf(self::VIEW_URL, $entity_id, $entity_token);
        } catch (\Exception $e) {
            $this->logException($e);
            $msg = $e->getMessage();
            $redirectUrl = sprintf(self::REVIEW_URL, $entity_id, $entity_token);
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

        $this->layout("layout/user/ajax");
        $this->flashMessenger()->addMessage($msg);
        $redirectUrl = sprintf(self::VIEW_URL, $entity_id, $entity_token);
        $this->logInfo($msg);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');

        $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($id, $token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => \Procure\Domain\Shared\Constants::FORM_ACTION_SHOW,
            'form_action' => "/procure/po/view",
            'form_title' => $nmtPlugin->translate("Show PO"),
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/po/review-v1");
        $this->getLogger()->info(\sprintf("PO #%s viewed by #%s", $id, $u->getId()));

        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->getLogger()->info(\sprintf("PO #%s saved as format %s by #%s", $id, $file_type, $u->getId()));
        $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($id, $token, $file_type);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => \Procure\Domain\Shared\Constants::FORM_ACTION_SHOW,
            'form_action' => "/procure/po/view",
            'form_title' => $nmtPlugin->translate("Show PO"),
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/po/review-v1");
        return $viewModel;
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
        /**@var \Application\Entity\MlaUsers $u ;*/

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

        $notification = new Notification();
        try {

            $msg = null;
            $data = $prg;

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];
            $userId = $u->getId();

            $rootEntity = $this->purchaseOrderService->getPODetailsById($entity_id, $entity_token);

            if ($rootEntity == null) {
                $msg = sprintf("PO #%s not found", $entity_id);

                $redirectUrl = sprintf("/procure/po/review-amendment?entity_id=%s&token=%s", $entity_id, $entity_token);
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($redirectUrl));
                return $response;
            }

            $options = new PoAmendmentAcceptOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $dto = new PoDTO();

            $cmdHandler = new AcceptAmendmentCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();
            $this->getLogger()->info(\sprintf("PO amendment #%s accepted by #%s", $entity_id, $u->getId()));
        } catch (\Exception $e) {
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
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }

        if ($docStatus == null) :
            $docStatus = "posted";

            if ($sort_by == null) :
                $sort_by = "sysNumber";
        endif;
        endif;


        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $list = $this->getPoReporter()->getPoList($is_active, $currentState, $docStatus, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;
        $limit = null;
        $offset = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
            $offset = $paginator->getMinInPage() - 1;
        }

        $list = $this->getPoReporter()->getPoList($is_active, $currentState, $docStatus, null, $sort_by, $sort, $limit, $offset, $file_type);

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus
        ));

        $viewModel->setTemplate("procure/po/dto_list");
        return $viewModel;
    }

    // ===================================
    // Deprecated.
    // ===================================

    // ===================================
    // Setter and Getter.
    // ===================================

    /**
     *
     * @return \Procure\Service\PoService
     */
    public function getPoService()
    {
        return $this->poService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poService
     */
    public function setPoService(\Procure\Service\PoService $poService)
    {
        $this->poService = $poService;
    }

    /**
     *
     * @return \Procure\Service\PoService
     */
    public function getPoSearchService()
    {
        return $this->poSearchService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poSearchService
     */
    public function setPoSearchService(\Procure\Service\PoSearchService $poSearchService)
    {
        $this->poSearchService = $poSearchService;
    }

    /**
     *
     * @return \Procure\Application\Service\PO\POService
     */
    public function getPurchaseOrderService()
    {
        return $this->purchaseOrderService;
    }

    /**
     *
     * @param POService $purchaseOrderService
     */
    public function setPurchaseOrderService(POService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     *
     * @return \Procure\Application\Reporting\PO\PoReporter
     */
    public function getPoReporter()
    {
        return $this->poReporter;
    }

    /**
     *
     * @param PoReporter $poReporter
     */
    public function setPoReporter(PoReporter $poReporter)
    {
        $this->poReporter = $poReporter;
    }
}
