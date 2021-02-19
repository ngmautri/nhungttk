<?php
namespace Procure\Controller;

use MLA\Paginator;
use Monolog\Logger;
use Procure\Application\Reporting\PO\PoReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Controller\Contracts\AbstractGenericController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoReportController extends AbstractGenericController
{

    protected $reporter;

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

        $filter = new PoReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($docYear);
        $filter->setBalance($balance);
        $filter->setDocStatus($docStatus);

        $total_records = $this->getReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);

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
            'balance' => $balance
        ));

        $viewModel->setTemplate("procure/po-report/dto_list");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
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

        $filter = new PoReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocYear($docYear);
        $filter->setBalance($balance);
        $filter->setDocStatus($docStatus);
        $filter->setVendorId($vendorId);

        $total_records = $this->getReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);

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

        $viewModel->setTemplate("procure/po-report/dto_list_ajax");
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
        $docYear = $this->params()->fromQuery('docYear');
        $docStatus = $this->params()->fromQuery('docStatus');

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
            //$sort_by = "itemName";
         endif;

        if ($docYear == null) :
            $docYear = date('Y');
         endif;

        if ($sort == null) :
            $sort = "ASC";
         endif;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;
        $total_records = null;

        $filter = new PoReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($docYear);
        $filter->setDocStatus($docStatus);

        $key = \sprintf("total_%s", $filter->__toString());
        $total = $this->getCache()->getItem($key);
        if (! $total->isHit()) {

            $total_records = $this->getReporter()->getAllRowTotal($filter);
            $total->set($total_records);
            $this->getCache()->save($total);
        }

        if ($this->getCache()->hasItem($key)) {
            $total_records = $this->getCache()
                ->getItem($key)
                ->get();
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $result = $this->getReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $isActive,
            'per_pape' => $resultsPerPage,
            'balance' => $balance,
            'docYear' => $docYear,
            'file_type' => $file_type,
            'result' => $result,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function vendorRowStatusAction()
    {
        $this->layout("layout/user/ajax");

        $vendorId = (int) $this->params()->fromQuery('vendorId');

        // $this->layout("layout/fluid");
        $isActive = (int) $this->params()->fromQuery('is_active');
        $file_type = (int) $this->params()->fromQuery('file_type');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $docYear = $this->params()->fromQuery('docYear');
        $docStatus = $this->params()->fromQuery('docStatus');

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
            $file_type = SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE;
        endif;

        if ($balance == null) {
            $balance = 1;
        }

        if ($sort_by == null) :
        //$sort_by = "itemName";
        endif;

        if ($docYear == null) :
            $docYear = date('Y');
        endif;

        if ($sort == null) :
            $sort = "ASC";
        endif;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;
        $total_records = null;

        $filter = new PoReportSqlFilter();
        $filter->setVendorId($vendorId);
        $filter->setBalance(1);

        $total_records = $this->getReporter()->getAllRowTotal($filter);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }
        $result = $this->getReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

        $viewModel = new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $isActive,
            'per_pape' => $resultsPerPage,
            'balance' => $balance,
            'docYear' => $docYear,
            'file_type' => $file_type,
            'result' => $result,
            'paginator' => $paginator,
            'vendorId' => $vendorId
        ));

        $viewModel->setTemplate("procure/po-report/vendor-row-status");
        return $viewModel;
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

        $limit = null;
        $offset = null;

        $filter = new PoReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setDocYear($docYear);
        $filter->setDocStatus($docStatus);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

        $key = \sprintf("total_%s", $filter->__toString());
        $total = $this->getCache()->getItem($key);
        if (! $total->isHit()) {

            $total_records = $this->getReporter()->getAllRowTotal($filter);
            $total->set($total_records);
            $this->getCache()->save($total);
        }

        if ($this->getCache()->hasItem($key)) {
            $total_records = $this->getCache()
                ->getItem($key)
                ->get();
        }

        $a_json_final = array();

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
            }
        }

        $result = $this->getReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

        // var_dump($result);

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
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     *
     * @param PoReporter $reporter
     */
    public function setReporter(PoReporter $reporter)
    {
        $this->reporter = $reporter;
    }
}
