<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Reporting\Transaction\TrxReporter;
use Inventory\Infrastructure\Persistence\Filter\TrxReportSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter;
use MLA\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class StockReportController extends AbstractGenericController
{

    protected $trxReporter;

    public function headerStatusAction()
    {
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $file_type = $this->params()->fromQuery('file_type');
        $docYear = $this->params()->fromQuery('docYear');
        $docMonth = $this->params()->fromQuery('docMonth');

        if ($docYear == null) {
            $docYear = date("Y");
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

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $filter = new TrxReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);
        $total_records = $this->getTrxReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $list = $this->getTrxReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $list = null;
        }

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $isActive,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'filter' => $filter
        ));

        $viewModel->setTemplate("inventory/trx-report/dto_list");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function rowStatusAction()
    {
        // $this->layout("layout/fluid");
        $file_type = (int) $this->params()->fromQuery('file_type');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $docYear = $this->params()->fromQuery('docYear');
        $docMonth = $this->params()->fromQuery('docMonth');
        $isActive = $this->params()->fromQuery('isActive');

        if ($file_type == null) :
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
         endif;

        if ($sort_by == null) :
            $sort_by = "itemName";
         endif;

        if ($docYear == null) :
            $docYear = date('Y');
         endif;

        if ($sort == null) :
            $sort = "ASC";
         endif;

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

        $isActive = null;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        $filter = new TrxRowReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);

        $total_records = $this->getTrxReporter()->getAllRowTotal($filter);

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getTrxReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE) {
            $result = $this->getTrxReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'file_type' => $file_type,
            'result' => $result,
            'per_pape' => $resultsPerPage,
            'paginator' => $paginator,
            'filter' => $filter
        ));
    }

    public function inOutOnhandAction()
    {
        // $this->layout("layout/fluid");
        $file_type = (int) $this->params()->fromQuery('file_type');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $docYear = $this->params()->fromQuery('docYear');
        $docMonth = $this->params()->fromQuery('docMonth');
        $isActive = $this->params()->fromQuery('isActive');

        if ($file_type == null) :
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        endif;

        if ($sort_by == null) :
            $sort_by = "itemName";
        endif;

        if ($docYear == null) :
            $docYear = date('Y');
        endif;

        if ($sort == null) :
            $sort = "ASC";
        endif;

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

        $isActive = null;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        $filter = new TrxRowReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);

        $total_records = $this->getTrxReporter()->getAllRowTotal($filter);

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getTrxReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE) {
            $result = $this->getTrxReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'file_type' => $file_type,
            'result' => $result,
            'per_pape' => $resultsPerPage,
            'paginator' => $paginator,
            'filter' => $filter
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

        if (isset($_GET['is_active'])) {
            $isActive = (int) $_GET['is_active'];
        } else {
            $isActive = 1;
        }

        if (isset($_GET['docYear'])) {
            $docYear = $_GET['docYear'];
        } else {
            $docYear = date('Y');
        }

        if (isset($_GET['docMonth'])) {
            $docMonth = $_GET['docMonth'];
        } else {
            $docMonth = date('M');
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

        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;

        $filter = new TrxRowReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        $total_records = $this->getTrxReporter()->getAllRowTotal($filter);
        $a_json_final = array();

        if ($total_records > 0) {

            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
            }
        }
        $result = $this->getTrxReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);

        $a_json_final['data'] = $result;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Inventory\Application\Reporting\Transaction\TrxReporter
     */
    public function getTrxReporter()
    {
        return $this->trxReporter;
    }

    /**
     *
     * @param TrxReporter $trxReporter
     */
    public function setTrxReporter(TrxReporter $trxReporter)
    {
        $this->trxReporter = $trxReporter;
    }
}
