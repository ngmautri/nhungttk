<?php
namespace Procure\Controller;

use MLA\Paginator;
use Procure\Application\Reporting\GR\GrReporter;
use Procure\Controller\Contracts\ProcureCRUDController;
use Procure\Infrastructure\Persistence\Filter\GrReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 * Good Receipt Controller
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GrController extends ProcureCRUDController
{

    protected $grReporter;

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
        $this->baseUrl = '/procure/gr';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/gr/review-v1";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/gr/dto_list';
    }

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

        $isActive = (int) $this->params()->fromQuery('is_active');

        if ($isActive == null) {
            $isActive = 1;
        }

        if ($docStatus == null) {
            $docStatus = "posted";
        }

        if ($sort_by == null) {
            $sort_by = "sysNumber";
            $sort_by = "sysNumber";
        }

        if ($sort_by == null) {
            $sort_by = "createdOn";
        }

        if ($sort_by == null) {
            $sort_by = "createdOn";
        }

        $filter = new GrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocStatus($docStatus);

        $total_records = $this->getGrReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getGrReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
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

        $viewModel->setTemplate("procure/gr/dto_list");
        return $viewModel;
    }

    /**
     *
     * @return mixed
     */
    public function getGrReporter()
    {
        return $this->grReporter;
    }

    /**
     *
     * @param mixed $grReporter
     */
    public function setGrReporter(GrReporter $grReporter)
    {
        $this->grReporter = $grReporter;
    }
}
