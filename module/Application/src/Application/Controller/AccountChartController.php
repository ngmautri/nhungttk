<?php
namespace Application\Controller;

use Application\Notification;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\InsertDepartmentCmdHandler;
use Application\Application\Command\Doctrine\Company\AccountChart\CreateAccountCmdHandler;
use Application\Application\Command\Doctrine\Company\AccountChart\RemoveAccountCmdHandler;
use Application\Application\Command\Doctrine\Company\AccountChart\UpdateAccountCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\AccountChart\Tree\Output\AccountJsTreeFormatter;
use Application\Application\Service\AccountChart\Tree\Output\PureAccountWithRootForOptionFormatter;
use Application\Controller\Contracts\EntityCRUDController;
use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\GenericChart;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Export\ExportAsArray;
use Application\Domain\Util\Pagination\Paginator;
use Application\Form\AccountChart\AccountForm;
use Application\Form\AccountChart\ChartForm;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Escaper\Escaper;
use Zend\Http\Response;
use Zend\Stdlib\Hydrator\Reflection;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountChartController extends EntityCRUDController
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
        $this->baseUrl = '/application/account-chart';
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

        $prg = $this->prg($form_action, true);

        $form = new ChartForm("coa_create_form");
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/application/account-chart/list');
        $form->setFormAction($action);
        $form->refresh();

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $id = $this->params()->fromQuery('id');

            /**
             *
             * @var GenericChart $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($id);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $snapshot = $rootEntity->makeSnapshot();
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'departmentName' => null,
                'departmentNode' => null,
                'coaCode' => $id,
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
                'jsTree' => $rootEntity->createChartTree()
                    ->getRoot()
                    ->display(new AccountJsTreeFormatter()),
                'rootEntity' => $rootEntity
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new InsertDepartmentCmdHandler();
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
                'form' => $form
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = $this->getBaseUrl() . "/list2";

        return $this->redirect()->toUrl($redirectUrl);
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

        $form = new ChartForm("coa_create_form");
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/application/account-chart/list');
        $form->setFormAction($action);
        $form->refresh();

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentName = $this->params()->fromQuery('p');
            $snapshot = new DepartmentSnapshot();
            $snapshot->setParentName($parentName);
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
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new InsertDepartmentCmdHandler();
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
                'form' => $form
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

        $redirectUrl = $this->getBaseUrl() . "/list2";

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

        $form = new AccountForm("account_crud_form");
        $form->setAction($form_action);
        $form->setHydrator(new Reflection());
        $form->setFormAction($action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentCode = $this->params()->fromQuery('pid');
            $rootId = $this->params()->fromQuery('rid');
            $form->setRedirectUrl(\sprintf("/application/account-chart/view?id=%s", $rootId));

            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $root = $rootEntity->createChartTree()->getRoot();

            $opts = $root->display(new PureAccountWithRootForOptionFormatter());
            $form->setAccountOptions($opts);
            $form->refresh();

            $snapshot = new AccountSnapshot();
            $snapshot->setParentAccountNumber($parentCode);
            $snapshot->setCoa($rootId);
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'parentCode' => $parentCode,
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
        $data = $prg;

        $notification = null;
        $rootEntityId = $data['coa'];
        $parentCode = $data['parentAccountNumber'];
        $form->setRedirectUrl(\sprintf("/application/account-chart/view?id=%s", $rootEntityId));

        $rootEntity = $this->getEntityService()->getRootEntityById($rootEntityId);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $rootEntityToken = null;
        $version = null;

        $options = new CreateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $rootEntityId, $rootEntityToken, $version, $this->getUserId(), __METHOD__);
        $cmdHandler = new CreateAccountCmdHandler();
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

            $root = $rootEntity->createChartTree()->getRoot();
            $opts = $root->display(new PureAccountWithRootForOptionFormatter());
            $form->setAccountOptions($opts);
            $form->refresh();

            $snapshot = new AccountSnapshot();
            $snapshot->setParentAccountNumber($parentCode);
            $snapshot->setCoa($rootEntityId);
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'parentCode' => $parentCode,
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

        $redirectUrl = \sprintf("/application/account-chart/view?id=%s", $rootEntityId);

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

        $form = new AccountForm("account_crud_form");
        $form->setAction($form_action);
        $form->setHydrator(new Reflection());
        $form->setFormAction($action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $accountNumber = $this->params()->fromQuery('mid');
            $rootId = $this->params()->fromQuery('rid');
            $form->setRedirectUrl(\sprintf("/application/account-chart/view?id=%s", $rootId));

            /**
             *
             * @var GenericChart $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $memberEntity = $rootEntity->getAccountByNumber($accountNumber);
            if ($memberEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $root = $rootEntity->createChartTree()->getRoot();

            $opts = $root->display(new PureAccountWithRootForOptionFormatter());
            $form->setAccountOptions($opts);
            $form->refresh();

            $snapshot = $memberEntity->makeSnapshot();
            $form->bind($snapshot);

            $viewModel = new ViewModel(array(
                'errors' => null,
                'parentCode' => $snapshot->getParentAccountNumber(),
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

        $rootId = $data['coa'];
        $memberId = $data['id'];
        $form->setRedirectUrl(\sprintf("/application/account-chart/view?id=%s", $rootId));

        $rootEntity = $this->getEntityService()->getRootEntityById($rootId);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $memberEntity = $rootEntity->getAccountById($memberId);

        if ($memberEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $entityToken = null;
        $version = null;

        $options = new UpdateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $memberEntity, $rootId, $entityToken, $version, $this->getUserId(), __METHOD__);
        $cmdHandler = new UpdateAccountCmdHandler();
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

            $root = $rootEntity->createChartTree()->getRoot();
            $opts = $root->display(new PureAccountWithRootForOptionFormatter());
            $form->setAccountOptions($opts);
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

        $redirectUrl = \sprintf("/application/account-chart/view?id=%s", $rootId);

        return $this->redirect()->toUrl($redirectUrl);
    }

    public function removeMemberAction()
    {
        $response = $this->getResponse();
        $escaper = new Escaper();

        try {

            $rootId = $escaper->escapeHtml($_POST['rid']);
            $memberCode = $escaper->escapeHtml($_POST['mid']);

            /**
             *
             * @var GenericChart $rootEntity
             */
            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            if ($rootEntity == null) {
                throw new \InvalidArgumentException("Account chart [$rootId] not found!");
            }

            $memberEntity = $rootEntity->getAccountByNumber($memberCode);
            if ($memberEntity == null) {
                throw new \InvalidArgumentException("Account [$memberCode] not found!");
            }

            $entityToken = null;
            $version = null;
            $data = null;

            $options = new UpdateMemberCmdOptions($this->getCompanyVO(), $rootEntity, $memberEntity, $rootId, $entityToken, $version, $this->getUserId(), __METHOD__);

            $cmdHandler = new RemoveAccountCmdHandler();
            $cmdHanderDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHanderDecorator, $this->getEventBusService());
            $cmd->execute();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getTraceAsString());
            $this->logException($e);

            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(\json_encode("[Failed] Node name not removed.Please see log!"));
            return $response;
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(\json_encode("[OK] Node removed!"));
        return $response;
    }

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

        /**
         *
         * @var GenericChart $chart
         */
        $chart = $results->getLazyAccountChartCollection();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'chart' => $chart,
            'companyVO' => $this->getCompanyVO()
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    public function girdAction()
    {
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
        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getById($this->getCompanyId());

        $chart = $results->getLazyAccountChartCollection();

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
    public function accountGirdAction()
    {
        $rootId = $this->getGETparam('rid');
        $pq_curPage = $this->getGETparam('pq_curpage', 1);
        $pq_rPP = $this->getGETparam('pq_rpp', 100);

        /**
         *
         * @var GenericChart $chart ;
         */
        $chart = $this->getEntityService()->getRootEntityById($rootId);
        $accountCollection = $chart->getLazyAccountCollection();

        $total_records = $accountCollection->count();
        $data = $accountCollection;

        $limit = null;
        $offset = null;

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
                $offset = $paginator->getMinInPage() - 1;
            }
        }

        $data = new ArrayCollection($accountCollection->slice($offset, $limit));

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
