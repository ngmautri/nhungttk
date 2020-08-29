<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Reporting\Transaction\TrxReporter;
use Inventory\Infrastructure\Persistence\Filter\TrxReportSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter;
use MLA\Paginator;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxReportController extends AbstractGenericController
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

        if ($file_type == null) {
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        }

        if ($sort_by == null) {
            $sort_by = "itemName";
        }

        if ($sort == null) {
            $sort = "ASC";
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

        $paginator = null;
        $result = null;
        $limit = null;
        $offset = null;

        $filter = $this->_createRowFilter($this);

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
        $docStatus = $this->params()->fromQuery('docStatus');

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

        if ($docStatus == null) :
            $docStatus = 'posted';
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
        $filter->setDocStatus($docStatus);

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
        $itemId = null;
        $warehouseId = null;
        $sort_by = "postingDate";
        $sort = "ASC";
        $isActive = 1;
        $docYear = date('Y');
        $docStatus = 'posted';
        $docMonth = date('M');
        $pq_curPage = 1;
        $pq_rPP = 100;
        $limit = null;
        $offset = null;

        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        }

        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        }

        if (isset($_GET['is_active'])) {
            $isActive = (int) $_GET['is_active'];
        }

        if (isset($_GET['docYear'])) {
            $docYear = $_GET['docYear'];
        }

        if (isset($_GET['docStatus'])) {
            $docStatus = $_GET['docStatus'];
        }

        if (isset($_GET['docMonth'])) {
            $docMonth = $_GET['docMonth'];
        }
        if (isset($_GET["pq_curpage"])) {
            $pq_curPage = $_GET["pq_curpage"];
        }

        if (isset($_GET["pq_rpp"])) {
            $pq_rPP = $_GET["pq_rpp"];
        }

        if (isset($_GET['itemId'])) {
            $itemId = $_GET['itemId'];
        }

        if (isset($_GET['warehouseId'])) {
            $warehouseId = $_GET['warehouseId'];
        }

        $filter = new TrxRowReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);
        $filter->setDocStatus($docStatus);
        $filter->setItem($itemId);
        $filter->setWarehouseId($warehouseId);

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
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function itemTrxAction()
    {
        // $this->layout("layout/fluid");
        $this->layout("layout/user/ajax");

        $file_type = (int) $this->params()->fromQuery('file_type');
        $layout = $this->params()->fromQuery('layout');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

        if ($layout == 'ajax') {}

        if ($file_type == null) {
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        }

        if ($sort_by == null) {
            $sort_by = "itemName";
        }

        if ($sort == null) {
            $sort = "ASC";
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

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        $filter = $this->_createRowFilter($this);

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
     * @param AbstractController $controller
     * @return \Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter
     */
    private function _createRowFilter(AbstractController $controller)
    {
        $isActive = $controller->params()->fromQuery('isActive');
        $docYear = $controller->params()->fromQuery('docYear');
        $docMonth = $controller->params()->fromQuery('docMonth');
        $itemId = $controller->params()->fromQuery('itemId');
        $warehouseId = $controller->params()->fromQuery('warehouseId');

        if ($docYear == null) {
            $docYear = date('Y');
        }

        if ($isActive == null) {
            $isActive = 1;
        }

        $filter = new TrxRowReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocMonth($docMonth);
        $filter->setDocYear($docYear);
        $filter->setItemId($itemId);
        $filter->setWarehouseId($warehouseId);
        $filter->setDocStatus(Constants::DOC_STATUS_POSTED);
        return $filter;
    }

    // SETTER AND GETTER

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
