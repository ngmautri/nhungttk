<?php
namespace Procure\Controller;

use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Application\Service\Department\Tree\Output\PureDepartmentWithRootForOptionFormatter;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Procure\Controller\Contracts\ProcureCRUDController;
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
        $departmentOptions = $root->display(new PureDepartmentWithRootForOptionFormatter());
        $form->setDepartmentOptions($departmentOptions);

        $filter = new CompanyQuerySqlFilter();
        $filter->setCompanyId($this->getCompanyId());
        $collection = $this->getFormOptionCollection();
        $form->setWhOptions($collection->getWHCollection($filter));
        $form->refresh();
        return $form;
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

        $variables = [
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'errors' => null,
            'version' => $rootEntity->getRevisionNo(),
            'localCurrencyId' => $this->getLocalCurrencyId(),
            'defaultWarehouseId' => $this->getDefautWarehouseId(),
            'companyVO' => $this->getCompanyVO()
        ];

        $summaryViewModel = new ViewModel($variables);
        $summaryViewModel->setTemplate("procure/pr/pr-summary");

        $viewModel = new ViewModel($variables);
        $viewModel->addChild($summaryViewModel, 'summary');
        $viewModel->setTemplate("procure/pr/view-v2");
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
        $request = $this->getRequest();

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

        $viewModel = new ViewModel(array(
            'collectionRender' => $render,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $perPage,
            'filter' => $filter,
            'form' => $form
        ));

        $viewModel->setTemplate("procure/pr/row-content");
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
