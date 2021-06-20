<?php
namespace Application\Controller;

use Application\Notification;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\ItemAttribute\CreateAttributeCmdHandler;
use Application\Application\Command\Doctrine\Company\ItemAttribute\CreateAttributeGroupCmdHandler;
use Application\Application\Command\Doctrine\Company\Warehouse\LockWarehouseCmdHandler;
use Application\Application\Command\Doctrine\Company\Warehouse\RemoveLocationCmdHandler;
use Application\Application\Command\Doctrine\Company\Warehouse\UnLockWarehouseCmdHandler;
use Application\Application\Command\Doctrine\Company\Warehouse\UpdateLocationCmdHandler;
use Application\Application\Command\Doctrine\Company\Warehouse\UpdateWarehouseCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Command\Options\UpdateEntityCmdOptions;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\Warehouse\Tree\Output\PureWhLocationWithRootForOptionFormatter;
use Application\Application\Service\Warehouse\Tree\Output\WhLocationJsTreeFormatter;
use Application\Controller\Contracts\EntityCRUDController;
use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Company\Brand\GenericBrand;
use Application\Domain\Company\ItemAttribute\AttributeSnapshot;
use Application\Domain\Company\ItemAttribute\GenericAttributeGroup;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Export\ExportAsArray;
use Application\Domain\Util\Pagination\Paginator;
use Application\Form\Brand\BrandForm;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Zend\Escaper\Escaper;
use Zend\Http\Response;
use Zend\Hydrator\Reflection;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandController extends EntityCRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Controller\Contracts\CRUDController::setBaseUrl()
     */
    protected function setBaseUrl()
    {
        $this->baseUrl = '/application/brand';
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = "layout/user/ajax";
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Application/layout-fluid";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/list';
    }

    protected function setViewTemplate()
    {}

    public function viewAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Create Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // $prg = $this->prg($form_action, true);

        $form = $this->_createRootForm($form_action, $action);

        $id = $this->params()->fromQuery('id');

        /**
         *
         * @var GenericBrand $rootEntity
         */
        $rootEntity = $this->getEntityService()->getRootEntityById($id);
        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $snapshot = $rootEntity->makeSnapshot();
        $form->bind($snapshot);
        $form->disableForm();

        $viewModel = new ViewModel(array(
            'errors' => null,
            'redirectUrl' => null,
            'version' => null,
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'action' => $action,
            'sharedCollection' => $this->getSharedCollection(),
            'companyVO' => $this->getCompanyVO(),
            'form' => $form,
            'jsTree' => null,
            'rootEntity' => $rootEntity
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @param string $action
     * @return \Application\Form\Warehouse\WarehouseForm
     */
    private function _createRootForm($form_action, $action)
    {
        $form = new BrandForm("root_form_" . 'brand');
        $form->setAction($form_action);
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/application/brand/list');
        $form->setFormAction($action);

        $filter = new CompanyQuerySqlFilter();
        $filter->setCompanyId($this->getCompanyId());
        $form->refresh();
        return $form;
    }

    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        $form = $this->_createRootForm($form_action, $action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentName = $this->params()->fromQuery('p');
            $snapshot = new BrandSnapshot();
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'departmentName' => null,
                'departmentNode' => null,
                'parentName' => $parentName,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form,
                'jsTree' => null,
                'rootEntity' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new CreateAttributeGroupCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();

        $form->bind($cmd->getOutput());

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'departmentName' => null,
                'departmentNode' => null,
                'parentName' => null,
                'redirectUrl' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form,
                'jsTree' => null,
                'rootEntity' => null
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = $this->getBaseUrl() . "/list";

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function updateAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update";
        $form_title = "Create Form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        $form = $this->_createRootForm($form_action, $action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $rootId = $this->params()->fromQuery('id');
            $form->setRedirectUrl(\sprintf("/application/warehouse/view?id=%s", $rootId));

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            if ($rootEntity->getIsDefault()) {
                $this->flashMessenger()->addMessage("Default Warehouse can not be changed!");
                $redirectUrl = \sprintf("/application/warehouse/view?id=%s", $rootId);
                return $this->redirect()->toUrl($redirectUrl);
            }

            $snapshot = $rootEntity->makeSnapshot();
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,

                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form,
                'jsTree' => $rootEntity->createLocationTree()
                    ->getRoot()
                    ->display(new WhLocationJsTreeFormatter()),
                'rootEntity' => $rootEntity
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING:
        // ==========================
        $data = $prg;

        $notification = null;

        $rootId = $data['id']; // to update
        $form->setRedirectUrl(\sprintf("/application/warehouse/view?id=%s", $rootId));

        /**
         *
         * @var BaseWarehouse $rootEntity ;
         */
        $rootEntity = $this->getEntityService()->getRootEntityById($rootId);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $rootEntityToken = null;
        $version = null;

        $options = new UpdateEntityCmdOptions($this->getCompanyVO(), $rootEntity, $rootId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);
        $cmdHandler = new UpdateWarehouseCmdHandler();
        $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
        $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
        $cmd->setLogger($this->getLogger());

        try {
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        $form->bind($cmd->getOutput());

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'parentCode' => null,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form,
                'jsTree' => $rootEntity->createLocationTree()
                    ->getRoot()
                    ->display(new WhLocationJsTreeFormatter()),
                'rootEntity' => $rootEntity
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = \sprintf("/application/warehouse/view?id=%s", $rootId);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Controller\Contracts\EntityCRUDController::addMemberAction()
     */
    public function addMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/add-member";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud-member";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        // FORM
        $form = $this->_createMemberForm($form_action, $action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentCode = $this->params()->fromQuery('pid');
            $rootId = $this->params()->fromQuery('rid');
            $form->setRedirectUrl(\sprintf("/application/item-attribute/view?id=%s", $rootId));

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            // $root = $rootEntity->createLocationTree()->getRoot();

            // $opts = $root->display(new PureWhLocationWithRootForOptionFormatter());
            // $form->setLocationOptions($opts);
            $form->refresh();

            $snapshot = new AttributeSnapshot();
            $snapshot->setGroup($rootId);
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'group' => $rootId,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // =====================================
        // POSTING:
        // =====================================
        $data = $prg;

        $notification = null;
        $rootEntityId = $data['group'];
        $form->setRedirectUrl(\sprintf("/application/item-attribute/view?id=%s", $rootEntityId));

        $rootEntity = $this->getEntityService()->getRootEntityById($rootEntityId);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $rootEntityToken = null;
        $version = null;

        $options = new CreateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $rootEntityId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);
        $cmdHandler = new CreateAttributeCmdHandler();
        $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
        $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
        $cmd->setLogger($this->getLogger());

        try {
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        $form->bind($cmd->getOutput());

        if ($notification->hasErrors()) {

            $form->refresh();

            $snapshot = new AttributeSnapshot();
            $snapshot->setGroup($rootEntityId);
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'group' => $rootEntityId,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = \sprintf("/application/item-attribute/view?id=%s", $rootEntityId);

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function updateMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/update-member";
        $form_title = "Create Form";
        $action = FormActions::EDIT;
        $viewTemplete = $this->getBaseUrl() . "/crud-member";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        $form = $this->_createMemberForm($form_action, $action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $locationCode = $this->params()->fromQuery('mid');
            $rootId = $this->params()->fromQuery('rid');
            $form->setRedirectUrl(\sprintf("/application/warehouse/view?id=%s", $rootId));

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $memberEntity = $rootEntity->getLocationByCode($locationCode);
            if ($memberEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $root = $rootEntity->createLocationTree()->getRoot();

            $opts = $root->display(new PureWhLocationWithRootForOptionFormatter());
            $form->setLocationOptions($opts);
            $form->refresh();

            $snapshot = $memberEntity->makeSnapshot();
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'group' => $snapshot->get(),
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // POSTING:
        // ==========================
        $data = $prg;

        $notification = null;

        $rootId = $data['warehouse']; // to update
        $memberId = $data['id'];
        $form->setRedirectUrl(\sprintf("/application/warehouse/view?id=%s", $rootId));

        /**
         *
         * @var BaseWarehouse $rootEntity ;
         */
        $rootEntity = $this->getEntityService()->getRootEntityById($rootId);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $memberEntity = $rootEntity->getLocationById($memberId);

        if ($memberEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $entityToken = null;
        $version = null;

        $options = new UpdateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $memberEntity, $rootId, $entityToken, $version, $this->getUserId(), __METHOD__);
        $cmdHandler = new UpdateLocationCmdHandler();
        $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
        $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
        $cmd->setLogger($this->getLogger());

        try {
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        $form->bind($cmd->getOutput());

        if ($notification->hasErrors()) {

            $root = $rootEntity->createLocationTree()->getRoot();
            $opts = $root->display(new PureWhLocationWithRootForOptionFormatter());
            $form->setLocationOptions($opts);
            $form->refresh();

            $form->bind($cmd->getOutput());

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'parentCode' => null,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = \sprintf("/application/warehouse/view?id=%s", $rootId);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Controller\Contracts\EntityCRUDController::removeMemberAction()
     */
    public function removeMemberAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        try {

            $rootId = $escaper->escapeHtml($_POST['rid']);
            $memberCode = $escaper->escapeHtml($_POST['mid']);

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                throw new \InvalidArgumentException("WH Location [$rootId] not found!");
            }

            $memberEntity = $rootEntity->getLocationByCode($memberCode);
            if ($memberEntity == null) {
                throw new \InvalidArgumentException("WH Location [$memberCode] not found!");
            }

            $entityToken = null;
            $version = null;
            $data = null;

            $options = new UpdateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $memberEntity, $rootId, $entityToken, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = new RemoveLocationCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $this->logInfo($cmd->getNotification()
                ->successMessage(false));
        } catch (\Exception $e) {

            $this->logInfo($e->getMessage());
            $notification = new Notification();
            $notification->addError($e->getTraceAsString());

            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $m = $nmtPlugin->translate($e->getMessage());
            $response->setContent(\json_encode("[Bad request] " . $m));
            $this->flashMessenger()->addMessage('[Bad request] ' . $m);

            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Location Node removed!"));
        $this->flashMessenger()->addMessage("[OK] Location Node removed!");
        return $response;
    }

    public function lockAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        try {

            $rootId = $escaper->escapeHtml($_POST['rid']);

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                throw new \InvalidArgumentException("WH  [$rootId] not found!");
            }

            $rootEntityToken = null;
            $version = null;
            $data = null;
            $options = new UpdateEntityCmdOptions($this->getCompanyVO(), $rootEntity, $rootId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = new LockWarehouseCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $this->logInfo($cmd->getNotification()
                ->successMessage(false));
        } catch (\Exception $e) {

            $this->logInfo($e->getMessage());
            $notification = new Notification();
            $notification->addError($e->getTraceAsString());

            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $m = $nmtPlugin->translate($e->getMessage());
            $response->setContent(\json_encode("[Bad request] " . $m));
            $this->flashMessenger()->addMessage('[Bad request] ' . $m);

            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Warehouse Locked!"));
        $this->flashMessenger()->addMessage("[OK] Warehouse Locked!");
        return $response;
    }

    public function unlockAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        try {

            $rootId = $escaper->escapeHtml($_POST['rid']);

            /**
             *
             * @var GenericWarehouse $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                throw new \InvalidArgumentException("WH  [$rootId] not found!");
            }

            $rootEntityToken = null;
            $version = null;
            $data = null;
            $options = new UpdateEntityCmdOptions($this->getCompanyVO(), $rootEntity, $rootId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);
            $cmdHandler = new UnLockWarehouseCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();

            $this->logInfo($cmd->getNotification()
                ->successMessage(false));
        } catch (\Exception $e) {

            $this->logInfo($e->getMessage());
            $notification = new Notification();
            $notification->addError($e->getTraceAsString());

            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

            $m = $nmtPlugin->translate($e->getMessage());
            $response->setContent(\json_encode("[Bad request] " . $m));
            $this->flashMessenger()->addMessage('[Bad request] ' . $m);

            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Warehouse UnLocked!"));
        $this->flashMessenger()->addMessage("[OK] Warehouse UnLocked!");
        return $response;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $viewTemplete = $this->getViewTemplate();
        $request = $this->getRequest();

        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getById($this->getCompanyId());

        $resultCollection = $results->getLazyItemAttributeGroupCollection();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'resultCollection' => $resultCollection,
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    public function girdAction()
    {
        $pq_curPage = $this->getGETparam('pq_curpage', 1);
        $pq_rPP = $this->getGETparam('pq_rpp', 100);

        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getById($this->getCompanyId());

        $chart = $results->getLazyItemAttributeGroupCollection();

        $exporter = new ExportAsArray();
        $girdData = $exporter->execute($chart);

        $a_json_final['data'] = $girdData;
        $a_json_final['totalRecords'] = $chart->count();
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function attributeGirdAction()
    {
        $rootId = $this->getGETparam('rid');
        $pq_curPage = $this->getGETparam('pq_curpage', 1);
        $pq_rPP = $this->getGETparam('pq_rpp', 100);

        /**
         *
         * @var GenericAttributeGroup $entity ;
         */
        $entity = $this->getEntityService()->getRootEntityById($rootId);
        $resultCollection = $entity->getLazyAttributeCollection();

        $total_records = $resultCollection->count();
        $data = $resultCollection;

        $limit = null;
        $offset = null;

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
                $offset = $paginator->getMinInPage() - 1;
            }
        }

        $data = new ArrayCollection($resultCollection->slice($offset, $limit));

        $exporter = new ExportAsArray();
        $girdData = $exporter->execute($data);

        $a_json_final['data'] = $girdData;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }
}
