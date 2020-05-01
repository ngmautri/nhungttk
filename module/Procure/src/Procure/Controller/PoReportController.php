<?php
namespace Procure\Controller;

use MLA\Paginator;
use Monolog\Logger;
use Procure\Application\Reporting\PO\PoReporter;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoReportController extends AbstractActionController
{

    protected $reporter;

    protected $logger;

    public function headerStatusAction()
    {
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
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
        ;

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;

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

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setPrYear($prYear);
        $filter->setBalance($balance);

        $total_records = $this->getPrReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $list = $this->getPrReporter()->getListWithCustomDTO($filter, $sort_by, $sort, $limit, $offset, $file_type);

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
            'yy' => $prYear,
            'balance' => $balance
        ));

        $viewModel->setTemplate("procure/pr/dto_list");
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

        $filter = new PrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setBalance($balance);
        $filter->setPrYear($prYear);

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }
        $result = $this->getPrReporter()->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);

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
        $filter->setPrYear($prYear);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;
        $total_records = $this->getPrReporter()->getAllRowTotal($filter);
        $a_json_final = array();

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;
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
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @return \Procure\Application\Reporting\PO\PoReporter
     */
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
