<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Shared\Constants;
use Inventory\Application\Export\Item\Contracts\SaveAsSupportedType;
use Inventory\Application\Reporting\Item\ItemReporter;
use Inventory\Infrastructure\Persistence\Filter\InOutOnhandSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
use MLA\Paginator;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use DateTime;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportController extends AbstractGenericController
{

    protected $reporter;

    public function defaultAction()
    {
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $file_type = $this->params()->fromQuery('file_type');
        $layout = $this->params()->fromQuery('layout');

        $isActive = (int) $this->params()->fromQuery('isActive');
        $itemType = $this->params()->fromQuery('itemType');
        $isFixedAsset = (int) $this->params()->fromQuery('isFixedAsset');

        $resultsPerPage = (int) $this->params()->fromQuery('perPage');
        if ($resultsPerPage == 0) {
            $resultsPerPage = 15;
        }

        $page = $this->params()->fromQuery('page');
        if ($page == 0) {
            $page = 1;
        }

        if ($isActive == null) {
            $isActive = 1;
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        if ($file_type == null) :
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        endif;

        $filter = new ItemReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setItemType($itemType);

        $total_records = $this->getReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if ($file_type != SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $list = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
        } else {
            $list = null;
        }

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'perPage' => $resultsPerPage,
            'page' => $page,
            'file_type' => $file_type,
            'layout' => $layout,
            'filter' => $filter
        ));

        $viewModel->setTemplate("inventory/item-report/default");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function defaultGirdAction()
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

        if (isset($_GET['isActive'])) {
            $isActive = (int) $_GET['isActive'];
        } else {
            $isActive = 1;
        }

        if (isset($_GET['itemType'])) {
            $item_type = (int) $_GET['itemType'];
        } else {
            $item_type = 1;
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

        $filter = new ItemReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setItemType($item_type);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

        $total_records = $this->getReporter()->getListTotal($filter);

        $a_json_final = array();

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
            }
        }

        $result = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
        // var_dump($result);

        $a_json_final['data'] = $result;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    public function inOutOnhandAction()
    {
        // $this->layout("layout/fluid");
        $file_type = (int) $this->params()->fromQuery('file_type');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $nmtPlugin = $this->Nmtplugin();

        if ($file_type == null) :
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        endif;

        if ($sort_by == null) :
            $sort_by = "itemName";
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

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        $filter = $this->_createInOutOnhandFilter($this);

        $total_records = $this->getReporter()->getInOutOnhandTotal($filter);

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getReporter()->getInOutOnhand($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $result = $this->getReporter()->getInOutOnhand($filter, $sort_by, $sort, $limit, $offset, $file_type);

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'file_type' => $file_type,
            'result' => $result,
            'per_pape' => $resultsPerPage,
            'paginator' => $paginator,
            'filter' => $filter,
            'nmtPlugin' => $nmtPlugin
        ));
    }

    public function inOutOnhandGirdAction()
    {
        $warehouseId = null;
        $sort_by = "postingDate";
        $sort = "ASC";
        $isActive = 1;
        $fromDate = null;
        $toDate = null;
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

        if (isset($_GET['fromDate'])) {
            $fromDate = $_GET['fromDate'];
        }

        if (isset($_GET['toDate'])) {
            $toDate = $_GET['toDate'];
        }
        if (isset($_GET["pq_curpage"])) {
            $pq_curPage = $_GET["pq_curpage"];
        }

        if (isset($_GET["pq_rpp"])) {
            $pq_rPP = $_GET["pq_rpp"];
        }

        if (isset($_GET['warehouseId'])) {
            $warehouseId = $_GET['warehouseId'];
        }

        $filter = new InOutOnhandSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setWarehouseId($warehouseId);
        $filter->setFromDate($fromDate);
        $filter->setToDate($toDate);
        $filter->setDocStatus("posted");
        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        $total_records = $this->getReporter()->getInOutOnhandTotal($filter);
        $a_json_final = array();

        if ($total_records > 0) {

            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
            }
        }
        $result = $this->getReporter()->getInOutOnhand($filter, $sort_by, $sort, $limit, $offset, $file_type);

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
    private function _createInOutOnhandFilter(AbstractController $controller)
    {
        $isActive = (int) $controller->params()->fromQuery('isActive');
        $itemId = $controller->params()->fromQuery('itemId');
        $warehouseId = $controller->params()->fromQuery('warehouseId');
        $fromDate = $controller->params()->fromQuery('fromDate');
        $toDate = $controller->params()->fromQuery('toDate');

        if ($isActive == 0) {
            $isActive = 1;
        }

        $date = new DateTime();

        if ($fromDate == null) {
            $d = $date->modify('first day of this month');
            $fromDate = $d->format('Y-m-d');
        }

        if ($toDate == null) {
            $d = $date->modify('last day of this month');
            $toDate = $d->format('Y-m-d');
        }

        $filter = new InOutOnhandSqlFilter();
        $filter->setIsActive(1);
        $filter->setItemId($itemId);
        $filter->setWarehouseId($warehouseId);
        $filter->setDocStatus(Constants::DOC_STATUS_POSTED);
        $filter->setFromDate($fromDate);
        $filter->setToDate($toDate);
        return $filter;
    }

    /**
     *
     * @return \Inventory\Application\Reporting\Item\ItemReporter
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     *
     * @param ItemReporter $reporter
     */
    public function setReporter(ItemReporter $reporter)
    {
        $this->reporter = $reporter;
    }
}
