<?php
namespace Application\Controller;

use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\InsertDepartmentCmdHandler;
use Application\Application\Command\Doctrine\Company\AccountChart\CreateAccountCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\AccountChart\Tree\Output\AccountJsTreeFormatter;
use Application\Application\Service\AccountChart\Tree\Output\PureAccountWithRootForOptionFormatter;
use Application\Controller\Contracts\EntityCRUDController;
use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\GenericChart;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Export\ExportAsArray;
use Application\Form\AccountChart\AccountForm;
use Application\Form\AccountChart\ChartForm;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
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

    public function addMemberAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/add-member";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/add-member";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);

        $form = new AccountForm("acount_create_form");
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/application/account-chart/list');
        $form->setFormAction($action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $parentCode = $this->params()->fromQuery('pid');
            $rootId = $this->params()->fromQuery('rid');

            $rootEntity = $this->getEntityService()->getRootEntityById($rootId);
            $root = $rootEntity->createChartTree()->getRoot();
            $opts = $root->display(new PureAccountWithRootForOptionFormatter());
            $form->setAccountOptions($opts);
            $form->refresh();

            $snapshot = new AccountSnapshot();
            $snapshot->setParentAccountNumber($parentCode);
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

        $notification = null;
        try {

            $data = $prg;

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new CreateAccountCmdHandler();
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

        $redirectUrl = $this->getBaseUrl() . "/list";

        return $this->redirect()->toUrl($redirectUrl);
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
}
