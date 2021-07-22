<?php
namespace Procure\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Util\Pagination\Paginator;
use Procure\Application\Reporting\AP\ApReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Infrastructure\Persistence\Filter\ApReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApReportController extends AbstractGenericController
{

    protected $apReporter;

    public function getOfVendorAction()
    {
        // ===============================
        $request = $this->getRequest();

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $isActive = (int) $this->params()->fromQuery('is_active');
        $vendorId = (int) $this->params()->fromQuery('vendorId');

        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');
        $docYear = $this->params()->fromQuery('docYear');
        $balance = $this->params()->fromQuery('balance');

        if ($docYear == null) {
            // $docYear = date("Y");
        }

        if ($docStatus == null) {
            $docStatus = ProcureDocStatus::POSTED;
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

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $filter = new ApReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($docYear);
        $filter->setBalance($balance);
        $filter->setDocStatus($docStatus);
        $filter->setVendorId($vendorId);

        $total_records = $this->getApReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
            $offset = $paginator->getMinInPage() - 1;
        }

        $list = $this->getApReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $isActive,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus,
            'yy' => $docYear,
            'balance' => $balance,
            'isAjaxRequest' => true,
            'vendorId' => $vendorId
        ));

        $viewModel->setTemplate("procure/ap-report/dto_list_ajax");
        return $viewModel;
    }

    public function headerStatusAction()
    {
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');
        $docYear = $this->params()->fromQuery('docYear');
        $balance = $this->params()->fromQuery('balance');

        if ($docYear == null) {
            $docYear = date("Y");
        }

        if ($docStatus == null) {
            $docStatus = ProcureDocStatus::POSTED;
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

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $filter = new ApReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($docYear);
        $filter->setBalance($balance);
        $filter->setDocStatus($docStatus);

        $total_records = $this->getApReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
            $offset = $paginator->getMinInPage() - 1;
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $list = $this->getApReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
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
            'currentState' => $currentState,
            'docStatus' => $docStatus,
            'docYear' => $docYear,
            'balance' => $balance
        ));

        $viewModel->setTemplate("procure/ap-report/dto_list");
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
        $balance = $this->params()->fromQuery('balance');
        $docYear = $this->params()->fromQuery('docYear');
        $docStatus = $this->params()->fromQuery('docStatus');
        $isActive = (int) $this->params()->fromQuery('is_active');

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

        $balance = null;
        $is_active = null;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;

        $filter = new ApReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($docYear);
        $filter->setDocStatus($docStatus);

        $total_records = $this->getApReporter()->getAllRowTotal($filter);

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getApReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1;
            $offset = $paginator->getMinInPage() - 1;
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE) {
            $result = $this->getApReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'balance' => $balance,
            'file_type' => $file_type,
            'docYear' => $docYear,
            'docStatus' => $docStatus,
            'result' => $result,
            'per_pape' => $resultsPerPage,
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

        if (isset($_GET['docYear'])) {
            $docYear = $_GET['docYear'];
        } else {
            $docYear = date('Y');
        }

        if (isset($_GET['docStatus'])) {
            $docStatus = $_GET['docStatus'];
        } else {
            $docStatus = ProcureDocStatus::POSTED;
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

        $vendor_id = null;
        $item_id = null;
        $is_active = null;
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;

        $filter = new ApReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($docYear);
        $filter->setDocStatus($docStatus);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        $total_records = $this->getApReporter()->getAllRowTotal($filter);
        $a_json_final = array();

        if ($total_records > 0) {

            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
            }
        }
        $result = $this->getApReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

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
     * @return \Procure\Application\Reporting\AP\ApReporter
     */
    public function getApReporter()
    {
        return $this->apReporter;
    }

    /**
     *
     * @param ApReporter $apReporter
     */
    public function setApReporter(ApReporter $apReporter)
    {
        $this->apReporter = $apReporter;
    }
}
