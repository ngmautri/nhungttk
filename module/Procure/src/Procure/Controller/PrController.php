<?php
namespace Procure\Controller;

use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Application\Service\Department\Tree\Output\DepartmentWithOneLevelForOptionFormatter1;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Controller\Contracts\ProcureCRUDController;
use Procure\Domain\DocSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Form\PR\PRHeaderForm;
use Procure\Form\PR\PRRowCollectionFilterForm;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Zend\Hydrator\Reflection;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends ProcureCRUDController
{

    private $defaultPerPage = 15;

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
        $this->baseUrl = '/procure/pr';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/pr/review-v2";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/pr/list';
    }

    /**
     *
     * @param string $form_action
     * @param string $action
     * @return \Procure\Form\PR\PRHeaderForm
     */
    private function _createRootForm($form_action, $action)
    {
        $form = new PRHeaderForm("pr_create_form");
        $form->setAction($form_action);
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/procure/pr-report/header-status');
        $form->setFormAction($action);

        $builder = new DepartmentTree();
        $builder->setDoctrineEM($this->getDoctrineEM());
        $builder->initTree();
        $root = $builder->createTree(1, 0);

        // set up department
        $departmentOptions = $root->display(new DepartmentWithOneLevelForOptionFormatter1());
        $form->setDepartmentOptions($departmentOptions);

        $filter = new CompanyQuerySqlFilter();
        $filter->setCompanyId($this->getCompanyId());
        $collection = $this->getFormOptionCollection();
        $form->setWhOptions($collection->getWHCollection($filter));
        $form->refresh();
        return $form;
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

        $form = $this->_createRootForm($form_action, $action);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $this->layout("Procure/layout-fullscreen");

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');

            $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $headerDTO = $rootEntity->makeSnapshot();
            $form->bind($headerDTO);
            // $form->disableForm();

            $variables = [
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $headerDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ];

            $viewModel = new ViewModel($variables);
            $viewModel->setTemplate("procure/pr/view-v2");

            $sideBarButtonViewModel = new ViewModel();
            $sideBarButtonViewModel->setTemplate("procure/pr/sidebar-buttons");
            $viewModel->addChild($sideBarButtonViewModel, 'sidebar_buttons');

            $summaryViewModel = new ViewModel($variables);
            $summaryViewModel->setTemplate("procure/pr/pr-summary");
            $viewModel->addChild($summaryViewModel, 'summary');

            $headerFormModel = new ViewModel($variables);
            $headerFormModel->setTemplate("procure/pr/header-form");
            $viewModel->addChild($headerFormModel, 'header_form');

            $wizardModelVariables = [
                'current_step' => "STEP3"
            ];
            $wizardModel = new ViewModel($variables);
            $wizardModel->setVariables($wizardModelVariables);
            $wizardModel->setTemplate($this->getBaseUrl() . "/pr-create-wizard");
            $viewModel->addChild($wizardModel, 'wizard');

            $confirmPostingModel = new ViewModel();
            $confirmPostingModel->setTemplate("procure/common/posting-modal");
            $viewModel->addChild($confirmPostingModel, 'confirm_posting');

            return $viewModel;
        }

        // POSTING
        // ====================================
        try {

            $data = $prg;
            $entity_id = $data['id'];
            $entity_token = $data['token'];
            $version = $data['revisionNo'];

            $rootEntity = $this->procureService->getDocDetailsByTokenId($entity_id, $entity_token, null, $this->getLocale());

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
            $this->logInfo($e->getMessage());
            $this->logException($e);
            $msg = $e->getMessage();
            $redirectUrl = sprintf($this->getBaseUrl() . "/review?entity_id=%s&entity_token=%s", $entity_id, $entity_token);
        }

        if ($cmd->getNotification()->hasErrors()) {
            $headerDTO = $rootEntity->makeSnapshot();

            $form->bind($headerDTO);

            $variables = [
                'errors' => $cmd->getNotification()->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $headerDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'version' => $rootEntity->getRevisionNo(),
                'action' => $action,
                'localCurrencyId' => $this->getLocalCurrencyId(),
                'defaultWarehouseId' => $this->getDefautWarehouseId(),
                'companyVO' => $this->getCompanyVO(),
                'form' => $form
            ];

            $viewModel = new ViewModel($variables);
            $viewModel->setTemplate("procure/pr/view-v2");

            $summaryViewModel = new ViewModel($variables);
            $summaryViewModel->setTemplate("procure/pr/pr-summary");

            $headerFormModel = new ViewModel($variables);
            $headerFormModel->setTemplate("procure/pr/header-form");

            $viewModel->addChild($summaryViewModel, 'summary');
            $viewModel->addChild($headerFormModel, 'header_form');

            $wizardModelVariables = [
                'current_step' => "STEP3"
            ];
            $wizardModel = new ViewModel($variables);
            $wizardModel->setVariables($wizardModelVariables);
            $wizardModel->setTemplate($this->getBaseUrl() . "/pr-create-wizard");
            $viewModel->addChild($wizardModel, 'wizard');

            $confirmPostingModel = new ViewModel();
            $confirmPostingModel->setTemplate("procure/common/posting-modal");
            $viewModel->addChild($confirmPostingModel, 'confirm_posting');

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
     * {@inheritdoc}
     * @see \Procure\Controller\Contracts\ProcureCRUDController::createAction()
     */
    public function createAction()
    {
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = $this->getBaseUrl() . "/crud-header-view";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $form = $this->_createRootForm($form_action, $action);

        $prg = $this->prg($form_action, true);

        // Model Variable

        $modelViewVariables = [
            'errors' => null,
            'redirectUrl' => null,
            'entity_id' => null,
            'entity_token' => null,
            'version' => null,
            'rootEntity' => null,
            'headerDTO' => null,
            'nmtPlugin' => $nmtPlugin,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'action' => $action,
            'sharedCollection' => $this->getSharedCollection(),
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO(),
            'form' => $form
        ];

        $wizardModelVariables = [
            'current_step' => "STEP1"
        ];

        $wizardModel = new ViewModel($modelViewVariables);
        $wizardModel->setVariables($wizardModelVariables);
        $wizardModel->setTemplate($this->getBaseUrl() . "/pr-create-wizard");

        $headerFormModel = new ViewModel();
        $headerFormModel->setTemplate($this->getBaseUrl() . "/header-form");

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $snapshot = new PRSnapshot();
            $countryId = $this->getCompanyVO()->getCountry();
            $snapshot->setCompany($countryId);
            $form->bind($snapshot);

            $viewModel = new ViewModel($modelViewVariables);
            $viewModel->setTemplate($viewTemplete);

            $viewModel->addChild($wizardModel, 'wizard');

            $headerFormModel->setVariables($modelViewVariables);
            $viewModel->addChild($headerFormModel, 'header_form');

            $sideBarButtonViewModel = new ViewModel();
            $sideBarButtonViewModel->setTemplate("procure/pr/sidebar-buttons");
            $viewModel->addChild($sideBarButtonViewModel, 'sidebar_buttons');

            return $viewModel;
        }

        $notification = null;
        try {

            $data = $prg;

            // var_dump($data);

            $options = new CreateHeaderCmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = $this->getCmdHandlerFactory()->getCreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();
        if ($notification->hasErrors()) {

            $form->bind($cmd->getOutput());
            $modelViewVariables['errors'] = $notification->getErrors();
            $modelViewVariables['headerDTO'] = $cmd->getOutput();

            $viewModel = new ViewModel($modelViewVariables);
            $viewModel->setTemplate($viewTemplete);
            $viewModel->addChild($wizardModel, 'wizard');

            $headerFormModel->setVariables($modelViewVariables);
            $viewModel->addChild($headerFormModel, 'header_form');

            $sideBarButtonViewModel = new ViewModel();
            $sideBarButtonViewModel->setTemplate("procure/pr/sidebar-buttons");
            $viewModel->addChild($sideBarButtonViewModel, 'sidebar_buttons');

            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));

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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function view1Action()
    {
        // $this->layout("Procure/layout-fluid");
        $this->layout($this->getDefaultLayout());

        $form_action = $this->getBaseUrl() . "/view-v2";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getProcureServiceV2()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());

        $headerDTO = $rootEntity->makeSnapshot();

        $form = $this->_createRootForm($form_action, $action);
        $form->bind($headerDTO);
        $form->disableForm();

        $variables = [
            'errors' => null,
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'headerDTO' => $headerDTO,
            'version' => $rootEntity->getRevisionNo(),
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO(),
            'form' => $form
        ];

        $viewModel = new ViewModel($variables);
        $viewModel->setTemplate("procure/pr/view-v2");

        $sideBarButtonViewModel = new ViewModel();
        $sideBarButtonViewModel->setTemplate("procure/pr/sidebar-buttons");
        $viewModel->addChild($sideBarButtonViewModel, 'sidebar_buttons');

        $summaryViewModel = new ViewModel($variables);
        $summaryViewModel->setTemplate("procure/pr/pr-summary");

        $headerFormModel = new ViewModel($variables);
        $headerFormModel->setTemplate("procure/pr/header-form");

        $viewModel->addChild($summaryViewModel, 'summary');
        $viewModel->addChild($headerFormModel, 'header_form');

        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function rowContentAction()
    {
        $this->layout("layout/user/ajax");
        $form_action = "/procure/pr/row-content";
        $action = FormActions::SHOW;
        $viewTemplete = "/procure/pr/row-content";
        // $request = $this->getRequest();

        // echo $this->getLocale();
        $isActive = $this->getGETparam('isActive');
        $page = $this->getGETparam('page', 1);
        $perPage = $this->getGETparam('resultPerPage', $this->defaultPerPage);
        $balance = $this->getGETparam('balance', 100);
        $sort_by = $this->getGETparam('sortBy', "createdOn");
        $sort = $this->getGETparam('sort', 'DESC');
        $renderType = $this->getGETparam('renderType', SupportedRenderType::PARAM_QUERY);

        $filter = new PrRowReportSqlFilter();
        $filter->setBalance($balance);
        $filter->setResultPerPage($perPage);
        $filter->setSort($sort);
        $filter->setSortBy($sort_by);

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());
        $render = $this->getProcureServiceV2()->getRowCollectionRender($rootEntity, $filter, $page, $perPage, $renderType, $this->getLocale());

        $form = new PRRowCollectionFilterForm("pr_row_filter_form");

        $f = $form_action . "?entity_token=%s&entity_id=%s&renderType=%s";
        $form->setAction(sprintf($f, $token, $id, $renderType));

        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('/procure/pr/view1');
        $form->setFormAction($action);
        $form->refresh();
        $form->bind($filter);

        $viewModelVariables = [
            'collectionRender' => $render,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $perPage,
            'filter' => $filter,
            'form' => $form,
            'rootEntity' => $rootEntity
        ];

        $viewModel = new ViewModel($viewModelVariables);
        $viewModel->setTemplate("procure/pr/row-content");

        $rowGirdModel = new ViewModel($viewModelVariables);
        $rowGirdModel->setTemplate("procure/pr/row-gird");
        $viewModel->addChild($rowGirdModel, 'row_gird');

        // $removeModalModel = new ViewModel();
        // $removeModalModel->setTemplate("procure/common/remove-modal");
        // $viewModel->addChild($removeModalModel, 'remove_modal');

        return $viewModel;
    }

    public function rowGirdAction()
    {
        $filter = new PrRowReportSqlFilter();

        $page = $this->getGETparam('pq_curpage', 1);
        $perPage = $this->getGETparam('pq_rpp', $this->defaultPerPage);

        $balance = $this->getGETparam('balance', 100);
        $sort_by = $this->getGETparam('sortBy', "createdOn");
        $sort = $this->getGETparam('sort', 'DESC');

        $filter = new PrRowReportSqlFilter();
        $filter->setBalance($balance);
        $filter->setResultPerPage($perPage);
        $filter->setSort($sort);
        $filter->setSortBy($sort_by);

        $id = (int) $this->getGETparam('entity_id');
        $token = $this->getGETparam('entity_token');

        $rootEntity = $this->getProcureServiceV2()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());
        $render = $this->getProcureServiceV2()->getRowCollectionRender($rootEntity, $filter, $page, $perPage, SupportedRenderType::AS_ARRAY, $this->getLocale());

        if ($render == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $a_json_final['data'] = $render->execute();
        $a_json_final['totalRecords'] = $render->getPaginator()->getTotalResults();
        $a_json_final['curPage'] = $page;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }
}
