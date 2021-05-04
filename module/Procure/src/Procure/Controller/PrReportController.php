<?php
namespace Procure\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Util\Pagination\Paginator;
use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Procure\Infrastructure\Persistence\Reporting\Filter\PrGrReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrReportController extends AbstractGenericController
{

    protected $prReporter;

    public function prGrAction()
    {
        // $this->layout("layout/fluid");
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

        $filter = $this->_createPrGrFilter();

        $totalRecords = $this->getPrReporter()->getPrGrReportTotal($filter);

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $result = $this->getPrReporter()->getPrGrReport($filter, $file_type, $totalRecords);
        }

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        if ($totalRecords > $resultsPerPage) {
            $paginator = new Paginator($totalRecords, $page, $resultsPerPage);

            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        // $filter->setCompanyId($this->getCompanyId());
        $filter->setLimit($limit);
        $filter->setOffset($offset);

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $result = $this->getPrReporter()->getPrGrReport($filter, $file_type, $totalRecords);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'per_pape' => $resultsPerPage,
            'file_type' => $file_type,
            'result' => $result,
            'paginator' => $paginator,
            'filter' => $filter
        ));
    }

    private function _createPrGrFilter()
    {
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sortBy = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $vendorId = $this->params()->fromQuery('vendor_id');
        $fromDate = $this->params()->fromQuery('fromDate');
        $toDate = $this->params()->fromQuery('toDate');

        if ($sortBy == null) :
            $sortBy = "warehouseName";
        endif;

        if ($sort == null) :
            $sort = SqlKeyWords::ASC;
        endif;

        $filter = new PrGrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setFromDate($fromDate);
        $filter->setToDate($toDate);
        $filter->setVendorId($vendorId);
        $filter->setSort($sort);
        $filter->setSortBy($sortBy);
        $filter->setDocStatus(\Procure\Domain\Contracts\ProcureDocStatus::POSTED);

        return $filter;
    }

    public function prGrGridAction()
    {
        $isActive = (int) $this->getGETparam('is_active');
        $sortBy = $this->getGETparam('sort_by', "vendorName");
        $sort = $this->getGETparam('sort', SqlKeyWords::ASC);
        $vendorId = (int) $this->getGETparam('vendor_id');
        $fromDate = $this->getGETparam('fromDate');
        $toDate = $this->getGETparam('toDate');

        $filter = new PrGrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setFromDate($fromDate);
        $filter->setToDate($toDate);
        $filter->setVendorId($vendorId);
        $filter->setSort($sort);
        $filter->setSortBy($sortBy);
        $filter->setDocStatus(\Procure\Domain\Contracts\ProcureDocStatus::POSTED);

        // \var_dump($filter->getSort());

        $page = $this->getGETparam("pq_curpage", 1);
        $resultsPerPage = $this->getGETparam("pq_rpp", 100);

        $limit = null;
        $offset = null;

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

        $a_json_final = [];

        $total_records = $this->getPrReporter()->getPrGrReportTotal($filter);

        if ($total_records > 0) {
            if ($total_records > $resultsPerPage) {
                $paginator = new Paginator($total_records, $page, $resultsPerPage);

                $limit = $paginator->getLimit();
                $offset = $paginator->getOffset();
            }
        }
        $filter->setLimit($limit);
        $filter->setOffset($offset);

        $result = $this->getPrReporter()->getPrGrReport($filter, $file_type, $total_records);

        // var_dump($result);

        $a_json_final['data'] = $result;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $page;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function headerStatusAction()
    {
        // echo $this->getLocale();
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');
        $prYear = $this->params()->fromQuery('yy');
        $balance = $this->params()->fromQuery('balance');

        if ($prYear == null) {
            $prYear = date("Y");
        }

        if ($balance == null) {
            $balance = 1;
        }

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
            $docStatus = 'posted';
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($prYear);
        $filter->setBalance($balance);
        $filter->setDocStatus($docStatus);

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $list = $this->getPrReporter()->getListWithCustomDTO($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $list = null;
        }

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'filter' => $filter
        ));

        $viewModel->setTemplate("procure/pr-report/dto_list");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function rowStatusAction()
    {
        // $this->layout("layout/fluid");
        $isActive = (int) $this->params()->fromQuery('is_active');
        $file_type = (int) $this->params()->fromQuery('file_type');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $prYear = $this->params()->fromQuery('pr_year');

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

        if ($file_type == null) :
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        endif;

        if ($balance == null) {
            $balance = 1;
        }

        if ($sort_by == null) :
            $sort_by = "itemName";
         endif;

        if ($prYear == null) :
            $prYear = date('Y');
         endif;

        if ($sort == null) :
            $sort = "ASC";
         endif;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;
        $total_records = null;

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($prYear);

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);
        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getPrReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $result = $this->getPrReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $isActive,
            'per_pape' => $resultsPerPage,
            'balance' => $balance,
            'pr_year' => $prYear,
            'file_type' => $file_type,
            'result' => $result,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function rowStatusGirdAction()
    {
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        } else {
            $sort_by = "itemName";
        }

        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } else {
            $sort = "ASC";
        }

        if (isset($_GET['balance'])) {
            $balance = $_GET['balance'];
        } else {
            $balance = 1;
        }

        if (isset($_GET['is_active'])) {
            $isActive = (int) $_GET['is_active'];
        } else {
            $isActive = 1;
        }

        if (isset($_GET['pr_year'])) {
            $prYear = $_GET['pr_year'];
        } else {
            $prYear = date('Y');
        }

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

        $limit = null;
        $offset = null;

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($prYear);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);

        $a_json_final = array();

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);

                $limit = $paginator->getLimit();
                $offset = $paginator->getOffset();
            }
        }

        $result = $this->getPrReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);

        $a_json_final['data'] = $result;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    // ====================================
    // Setter and Getter.
    // ====================================
    /**
     *
     * @return \Procure\Application\Reporting\PR\PrReporter
     */
    public function getPrReporter()
    {
        return $this->prReporter;
    }

    /**
     *
     * @param PrReporter $prReporter
     */
    public function setPrReporter(PrReporter $prReporter)
    {
        $this->prReporter = $prReporter;
    }
}
