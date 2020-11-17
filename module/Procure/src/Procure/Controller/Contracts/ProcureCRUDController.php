<?php
namespace Procure\Controller\Contracts;

use Application\Notification;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\PO\PostCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\Contracts\ProcureServiceInterface;
use Procure\Domain\GenericDoc;
use Zend\Escaper\Escaper;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class ProcureCRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $viewTemplate;

    protected $listTemplate;

    protected $ajaxLayout;

    protected $cmdHandlerFactory;

    protected $procureService;

    /**
     *
     * @return mixed
     */
    public function getViewTemplate()
    {
        return $this->viewTemplate;
    }

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

    abstract protected function setListTemplate();

    abstract protected function setViewTemplate();

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/review";
        $form_title = "Edit Form";
        $action = FormActions::REVIEW;
        $viewTemplete = $this->getViewTemplate();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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
        // ====================================
        $notification = new Notification();
        $msg = null;
        try {

            $data = $prg;
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->procureService->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PostCmdOptions($rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = new PostCmdHandler();

            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator);
            $cmd->execute();

            $notification = $cmd->getNotification();
            $msg = sprintf("PO #%s is posted", $entity_id);
            $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $this->logException($e);
            $msg = $e->getMessage();
            $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
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
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);

        $this->logInfo($msg);
        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');

        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crudHeader";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CreateHeaderCmdOptions($this->getCompanyId(), $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getCreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

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
                'version' => null,
                'headerDTO' => $cmd->getOutput(),
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

        $header = $cmd->getOutput();
        if ($header instanceof GenericDoc) {
            $redirectUrl = sprintf($this->getBaseUrl() . "/add-row?target_token=%s&target_id=%s", $header->getToken(), $header->getId());
        } else {
            $redirectUrl = $this->getBaseUrl() . "/list";
        }

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update";
        $form_title = "Edit Form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudHeader";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('entity_token');
            $dto = $this->getProcureService()->getDocHeaderByTokenId($entity_id, $token);

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

        $notification = null;

        try {

            // POSTING
            $data = $prg;

            $rootEntityId = $data['entity_id'];
            $rootEntityToken = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getProcureService()->getDocHeaderByTokenId($rootEntityId, $rootEntityToken);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateHeaderCmdOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateHeaderCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
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
                'entity_id' => $rootEntityId,
                'entity_token' => $rootEntityToken,
                'headerDTO' => $cmd->getOutput(),
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
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $rootEntityId, $rootEntityToken);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addRowAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/add-row";
        $form_title = "Add row rorm";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudRow";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('target_token');
            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($target_id, $target_token);

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
        $notification = null;
        try {

            $data = $prg;

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $version = $data['docRevisionNo'];

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new CreateRowCmdOptions($rootEntity, $target_id, $target_token, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getCreateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());

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
                'target_id' => $target_id,
                'target_token' => $target_token,
                'dto' => $cmd->getOutput(),
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

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/add-row?target_id=%s&target_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("PO Row of %s is created by #%s", $target_id, $this->getUserId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateRowAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update-row";
        $form_title = "Update row form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crudRow";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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

            $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
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

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['docRevisionNo'];

            $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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

            // \var_dump($data);

            $options = new UpdateRowCmdOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());

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
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'docRevisionNo' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $cmd->getOutput(),
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
        $redirectUrl = sprintf("/procure/po/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("PO Row of %s is updated by #%s", $target_id, $this->getUserId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
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

                $target_id = $a['docId'];
                $target_token = $a['docToken'];
                $entity_id = $a['id'];
                $entity_token = $a['token'];
                $version = $a['docRevisionNo'];

                $result = $this->getProcureService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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

                $options = new UpdateRowCmdOptions($rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

                $cmdHandler = $this->getCmdHandlerFactory()->getInlineUpdateRowCmdHandler();
                $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
                $cmd = new GenericCommand($this->getDoctrineEM(), $a, $options, $cmdHanderDecorator, $this->getEventBusService());
                $cmd->execute();

                $notification = $cmd->getNotification();
            }
        } catch (\Exception $e) {
            $notification = new Notification();
            $notification->addError($e->getMessage());
            $this->getLogger()->error($e->getMessage());
        }

        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Doc row updated!"));
        return $response;
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
            $dto = $this->procureService->getDocDetailsByTokenId($entity_id, $token);

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
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateHeaderCmdOptions($rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getCloneCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $notification = $cmd->getNotification();
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
                'headerDTO' => $cmd->getOutput(),
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
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function saveAsAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Edit Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();

        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $nmtPlugin = $this->Nmtplugin();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $file_type = $this->params()->fromQuery('file_type');

        $this->logInfo(\sprintf("Document #%s saved as format %s by #%s", $id, $file_type, $this->getUserId()));
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, $file_type);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate($viewTemplete);

        return $viewModel;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLayout()
    {
        return $this->defaultLayout;
    }

    /**
     *
     * @return mixed
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    /**
     *
     * @return mixed
     */
    public function getAjaxLayout()
    {
        return $this->ajaxLayout;
    }

    /**
     *
     * @return \Procure\Application\Service\Contracts\ProcureServiceInterface
     */
    public function getProcureService()
    {
        return $this->procureService;
    }

    /**
     *
     * @param ProcureServiceInterface $procureService
     */
    public function setProcureService(ProcureServiceInterface $procureService)
    {
        $this->procureService = $procureService;
    }

    /**
     *
     * @param CmdHandlerAbstractFactory $cmdHandlerFactory
     */
    public function setCmdHandlerFactory(CmdHandlerAbstractFactory $cmdHandlerFactory)
    {
        $this->cmdHandlerFactory = $cmdHandlerFactory;
    }

    /**
     *
     * @return \Procure\Application\Command\Contracts\CmdHandlerAbstractFactory
     */
    public function getCmdHandlerFactory()
    {
        return $this->cmdHandlerFactory;
    }
}
