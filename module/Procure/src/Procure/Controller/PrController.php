<?php
namespace Procure\Controller;

use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Procure\Controller\Contracts\ProcureCRUDController;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends ProcureCRUDController
{

    private $defaultPerPage = 100;

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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function view1Action()
    {
        $this->layout("Procure/layout-fluid");
        $form_action = $this->getBaseUrl() . "/view-v2";
        $form_title = "Show Form";
        $action = FormActions::SHOW;
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());

        $viewModel = new ViewModel(array(
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
        ));

        $viewModel->setTemplate("procure/pr/view-v2");
        return $viewModel;
    }

    public function rowContentAction()
    {
        $this->layout("layout/user/ajax");
        $form_action = "/procure/pr-report/header-status-result";
        $form_title = "Item Serial Map";
        $action = FormActions::SHOW;
        $viewTemplete = "/procure/pr-report/header-status-result";
        $request = $this->getRequest();

        // echo $this->getLocale();
        $isActive = $this->getGETparam('isActive');
        $page = $this->getGETparam('page', 1);
        $perPage = $this->getGETparam('resultPerPage', $this->defaultPerPage);
        $balance = $this->params()->fromQuery('balance', 100);
        $sort_by = $this->params()->fromQuery('sortBy', "createdOn");
        $sort = $this->params()->fromQuery('$sort', "DESC");
        $renderType = $this->getGETparam('render_type', SupportedRenderType::PARAM_QUERY);

        $filter = new PrRowReportSqlFilter();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());
        $render = $this->getProcureService()->getRowCollectionRender($rootEntity, $filter, $page, $perPage, $renderType, $this->getLocale());

        $viewModel = new ViewModel(array(
            'rowCollectionRender' => $render,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $perPage,
            'filter' => $filter
        ));

        $viewModel->setTemplate("procure/pr/row-content");
        return $viewModel;
    }

    public function rowGirdAction()
    {
        $filter = new PrRowReportSqlFilter();

        $id = (int) $this->getGETparam('entity_id');
        $token = $this->getGETparam('entity_token');

        $page = $this->getGETparam('pq_curpage', 1);

        $perPage = $this->getGETparam('pq_rpp', $this->defaultPerPage);

        $rootEntity = $this->getProcureService()->getDocDetailsByTokenId($id, $token, null, $this->getLocale());
        $render = $this->getProcureService()->getRowCollectionRender($rootEntity, $filter, $page, $perPage, SupportedRenderType::AS_ARRAY, $this->getLocale());

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
