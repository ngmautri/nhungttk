<?php
namespace Inventory\Controller\Contracts;

use Application\Notification;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\PostCmdOptions;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\FileExtension;
use Application\Domain\Util\JsonErrors;
use Application\Domain\Util\Pagination\Paginator;
use Inventory\Application\Command\TransactionalCommandHandler;
use Inventory\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Service\Contracts\TrxServiceInterface;
use Inventory\Application\Service\Upload\Transaction\UploadFactory;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Domain\DocSnapshot;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class TrxCRUDController extends AbstractGenericController
{

    protected $baseUrl;

    protected $defaultLayout;

    protected $viewTemplate;

    protected $listTemplate;

    protected $ajaxLayout;

    protected $cmdHandlerFactory;

    protected $trxService;

    protected $transactionTypes;

    /**
     *
     * @return mixed
     */
    public function getViewTemplate()
    {
        return $this->viewTemplate;
    }

    abstract protected function redirectForView($movementType, $id, $token);

    abstract protected function redirectForCreate($data);

    abstract protected function setBaseUrl();

    abstract protected function setDefaultLayout();

    abstract protected function setAjaxLayout();

    abstract protected function setListTemplate();

    abstract protected function setViewTemplate();

    abstract protected function setTransactionTypes();

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
        $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $movementType = $rootEntity->getMovementType();
        $this->redirectForView($movementType, $rootEntity->getId(), $rootEntity->getToken()); // redirect

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
            'nmtPlugin' => $nmtPlugin,
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO(),
            'transactionType' => $this->getTransactionTypes()
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
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $data = $prg;
            $this->redirectForCreate($data); // redirect

            $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getCreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }
        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'headerDTO' => $cmd->getOutput(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($cmd->getNotification()
            ->successMessage(false));

        $header = $cmd->getOutput();

        if ($header instanceof DocSnapshot) {
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
            $dto = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $movementType = $dto->getMovementType();
            $this->redirectForView($movementType, $dto->getId(), $dto->getToken());

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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            // POSTING
            $data = $prg;

            $rootEntityId = $data['entity_id'];
            $rootEntityToken = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($rootEntityId, $rootEntityToken);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateHeaderCmdOptions($this->getCompanyVO(), $rootEntity, $rootEntityId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateHeaderCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
            // left blank
        }

        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $rootEntityId,
                'entity_token' => $rootEntityToken,
                'headerDTO' => $cmd->getOutput(),
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($cmd->getNotification()
            ->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $rootEntityId, $rootEntityToken);
        // $this->flashMessenger()->addMessage($redirectUrl);

        return $this->redirect()->toUrl($redirectUrl);
    }

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

            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING
        // ====================================
        try {

            $data = $prg;
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $version = $data['version'];

            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new PostCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getPostCmdHandler();

            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->execute();

            $msg = sprintf($cmd->getNotification()->successMessage());
            $redirectUrl = sprintf($this->getBaseUrl() . "/view?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        } catch (\Exception $e) {
            $this->logException($e);
            $msg = $e->getMessage();
            $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        }

        if ($cmd->getNotification()->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
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
            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($target_id, $target_token);

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
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete . $rootEntity->getMovementType());
            return $viewModel;
        }

        // ==============
        try {

            $data = $prg;

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $version = $data['docRevisionNo'];

            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new CreateRowCmdOptions($this->getCompanyVO(), $rootEntity, $target_id, $target_token, $version, $this->getUserId(), __METHOD__);
            $options->setLocale($this->getLocale());
            $cmdHandler = $this->getCmdHandlerFactory()->getCreateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {

            $this->logInfo($e->getMessage());
            $this->logException($e);
        }

        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
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
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete . $rootEntity->getMovementType());
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($cmd->getNotification()
            ->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/add-row?target_id=%s&target_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("Row of %s is created by #%s", $target_id, $this->getUserId()));

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

            $result = $this->getTrxService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $this->getLocale());

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if ($rootDTO == null || $localDTO == null) {
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
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete . $rootDTO->getMovementType());
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

            $result = $this->getTrxService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateRowCmdOptions($this->getCompanyVO(), $rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);
            $options->setLocale($this->getLocale());

            $cmdHandler = $this->getCmdHandlerFactory()->getUpdateRowCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
        } catch (\Exception $e) {}

        if ($cmd->getNotification()->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $cmd->getNotification()->getErrors(),
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
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'transactionType' => $this->getTransactionTypes()
            ));

            $viewModel->setTemplate($viewTemplete . $rootDTO->getMovementType());
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($cmd->getNotification()
            ->successMessage(false));
        $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $target_id, $target_token);
        $this->getLogger()->info(\sprintf("Trx Row of %s is updated by #%s", $target_id, $this->getUserId()));

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function inlineUpdateRowAction()
    {
        /*
         * $a_json_final = array();
         * $escaper = new Escaper();
         */
        $sent_list = json_decode($_POST['sent_list'], true);
        $to_update = $sent_list['addList'];

        // $this->getLogger()->info(\serialize($to_update));
        $response = $this->getResponse();

        try {
            foreach ($to_update as $a) {

                $target_id = $a['docId'];
                $target_token = $a['docToken'];
                $entity_id = $a['id'];
                $entity_token = $a['token'];
                $version = $a['docRevisionNo'];

                $result = $this->getTrxService()->getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

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

                $options = new UpdateRowCmdOptions($this->getCompanyVO(), $rootEntity, $localEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

                $cmdHandler = $this->getCmdHandlerFactory()->getInlineUpdateRowCmdHandler();
                $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);
                $cmd = new GenericCommand($this->getDoctrineEM(), $a, $options, $cmdHanderDecorator, $this->getEventBusService());
                $cmd->execute();
            }
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
            $this->logException($e);
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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId()
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

            $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $options = new UpdateHeaderCmdOptions($this->getCompanyVO(), $rootEntity, $entity_id, $entity_token, $version, $this->getUserId(), __METHOD__);

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
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId()
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
        $rootEntity = $this->getTrxService()->getDocDetailsByTokenId($id, $token, $file_type);

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
            'nmtPlugin' => $nmtPlugin,
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO(),
            'transactionType' => $this->getTransactionTypes()
        ));

        $viewModel->setTemplate($viewTemplete);

        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function uploadRowsAction()
    {
        /**
         *
         * @var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;
         */
        $this->layout("Inventory/layout-fullscreen");
        $nmtPlugin = $this->Nmtplugin();
        $form_action = $this->getBaseUrl() . "/upload-rows";

        $form_title = "Upload";
        $action = FormActions::UPLOAD;
        $viewTemplete = $this->getBaseUrl() . "/review-v1";
        $transactionType = $this->getTransactionTypes();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $notify = new Notification();

            try {

                $entity_id = $request->getPost('entity_id');
                $entity_token = $request->getPost('entity_token');

                $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

                if ($rootEntity == null) {
                    return $this->redirect()->toRoute('not_found');
                }

                $file_name = null;
                $file_size = null;
                $file_tmp = null;
                $file_ext = null;
                $file_type = null;

                if (isset($_FILES['uploaded_file'])) {
                    $file_name = $_FILES['uploaded_file']['name'];
                    $file_size = $_FILES['uploaded_file']['size'];
                    $file_tmp = $_FILES['uploaded_file']['tmp_name'];
                    $file_type = $_FILES['uploaded_file']['type'];
                    $file_ext1 = (explode('.', $file_name));
                    $file_ext = end($file_ext1);
                }

                $ext = FileExtension::get($file_type);
                if ($ext == null) {
                    $ext = \strtolower($file_ext);
                }
                $expensions = array(
                    "xlsx",
                    "xls",
                    "csv"
                );

                if (in_array($ext, $expensions) == false) {
                    $notify->addError("File not supported or empty! " . $file_name);
                }

                $dto = $rootEntity->makeSnapshot();

                if ($notify->hasErrors()) {
                    $viewModel = new ViewModel(array(
                        'errors' => $notify->getErrors(),
                        'redirectUrl' => null,
                        'entity_id' => $entity_id,
                        'entity_token' => $entity_token,
                        'headerDTO' => $dto,
                        'version' => $rootEntity->getRevisionNo(),
                        'nmtPlugin' => $nmtPlugin,
                        'form_action' => $form_action,
                        'form_title' => $form_title,
                        'action' => $action,
                        'rowOutput' => $rootEntity->getRowsOutput(),
                        'companyVO' => $this->getCompanyVO(),
                        'transactionType' => $this->getTransactionTypes()
                    ));
                    $viewModel->setTemplate($viewTemplete);
                    return $viewModel;
                }

                $folder = ROOT . "/temp";

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true); // important
                }

                move_uploaded_file($file_tmp, "$folder/$file_name");

                $uploader = UploadFactory::create($rootEntity->getMovementType());
                $uploader->setDoctrineEM($this->getDoctrineEM());
                $uploader->setLogger($this->getLogger());
                $uploader->doUploading($rootEntity, "$folder/$file_name");
            } catch (\Exception $e) {
                $this->logException($e, false);
                $notify->addError($e->getMessage());

                $viewModel = new ViewModel(array(
                    'errors' => $notify->getErrors(),
                    'redirectUrl' => null,
                    'entity_id' => $entity_id,
                    'entity_token' => $entity_token,
                    'headerDTO' => $dto,
                    'version' => $rootEntity->getRevisionNo(),
                    'nmtPlugin' => $nmtPlugin,
                    'form_action' => $form_action,
                    'form_title' => $form_title,
                    'action' => $action,
                    'rowOutput' => $rootEntity->getRowsOutput(),
                    'companyVO' => $this->getCompanyVO(),
                    'transactionType' => $this->getTransactionTypes()
                ));
                $viewModel->setTemplate($viewTemplete);
                return $viewModel;
            }

            $this->logInfo(\sprintf('Trx rows for #%s uploaded!, (%s-%s)', $rootEntity->getId(), $file_name, $file_size));
            $this->flashMessenger()->addMessage($notify->successMessage(false));
            $redirectUrl = sprintf($this->getBaseUrl() . '/review?entity_id=%s&entity_token=%s', $entity_id, $entity_token);
            \unlink("$folder/$file_name");
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO Posting
        // =========================

        $entity_id = (int) $this->params()->fromQuery('target_id');
        $entity_token = $this->params()->fromQuery('target_token');
        $rootEntity = $this->getTrxService()->getDocHeaderByTokenId($entity_id, $entity_token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $dto = $rootEntity->makeSnapshot();

        $viewModel = new ViewModel(array(
            'errors' => null,
            'redirectUrl' => null,
            'entity_id' => $entity_id,
            'entity_token' => $entity_token,
            'headerDTO' => $dto,
            'version' => $rootEntity->getRevisionNo(),
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'action' => $action,
            'transactionType' => $transactionType,
            'rowOutput' => $rootEntity->getRowsOutput()
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function rowGirdAction()
    {
        try {
            if (isset($_GET["pq_curpage"])) {
                $pq_curPage = $_GET["pq_curpage"];
            } else {
                $pq_curPage = 1;
            }

            if (isset($_GET["pq_rpp"])) {
                $pq_rPP = $_GET["pq_rpp"];
            } else {
                $pq_rPP = 100;
            }

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $total_records = $this->getTrxService()->getTotalRows($entity_id, $entity_token);

            $a_json_final = [];
            $a_json_final['totalRecords'] = $total_records;
            $a_json_final['curPage'] = $pq_curPage;

            // $total_records = 873;
            $outputStrategy = SaveAsSupportedType::OUTPUT_IN_ARRAY;
            $limit = null;
            $offset = null;

            if ($total_records > 0) {

                if ($total_records > $pq_rPP) {
                    $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                    $offset = $paginator->getMinInPage() - 1;
                    $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
                }
            }
            $rootEntity = $this->getTrxService()->getLazyDocOutputByTokenId($entity_id, $entity_token, $offset, $limit, $outputStrategy);

            $a_json_final['data'] = $rootEntity->getRowsOutput();

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($a_json_final));
            $this->logInfo(\sprintf('Json Last error: %s', JsonErrors::getErrorMessage(json_last_error())));

            return $response;
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }

    /**
     *
     * @return \Inventory\Application\Command\Contracts\CmdHandlerAbstractFactory
     */
    public function getCmdHandlerFactory()
    {
        return $this->cmdHandlerFactory;
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
     * @return \Inventory\Application\Service\Contracts\TrxServiceInterface
     */
    public function getTrxService()
    {
        return $this->trxService;
    }

    /**
     *
     * @param TrxServiceInterface $trxService
     */
    public function setTrxService(TrxServiceInterface $trxService)
    {
        $this->trxService = $trxService;
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
     * @return mixed
     */
    public function getTransactionTypes()
    {
        return $this->transactionTypes;
    }
}
