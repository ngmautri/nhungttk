<?php
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Procure\Application\Reporting\AP\ApReporter;
use Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy;
use MLA\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApReportController extends AbstractActionController
{

    protected $apReporter;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function rowStatusAction()
    {
        //$this->layout("layout/fluid");

        $outputStrategy = (int) $this->params()->fromQuery('output');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $ap_year = $this->params()->fromQuery('ap_year');
        $ap_month = $this->params()->fromQuery('ap_month');
        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($outputStrategy == null) :
            $outputStrategy = ApRowStatusOutputStrategy::OUTPUT_IN_ARRAY;
         endif;

        if ($sort_by == null) :
            $sort_by = "itemName";
         endif;

        if ($ap_year == null) :
            $ap_year = date('Y');
         endif;
         
         if ($ap_month == null) :
         //$ap_month = date('m');
         endif;

        if ($sort == null) :
            $sort = "ASC";
         endif;

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 30;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $vendor_id = null;
        $item_id = null;
        $balance = null;
        $is_active = null;

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;
        
        if($outputStrategy==ApRowStatusOutputStrategy::OUTPUT_IN_EXCEL ||$outputStrategy==ApRowStatusOutputStrategy::OUTPUT_IN_OPEN_OFFICE){
            return $this->getApReporter()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy);
        }
        
        $total_records = $this->getApReporter()->getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;

            $result = $this->getApReporter()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy);
        } else {
            $result = $this->getApReporter()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy);
        }

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'balance' => $balance,
            'ap_year' => $ap_year,
            'ap_month' => $ap_month,
            'output' => $outputStrategy,
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
            $is_active = (int) $_GET['is_active'];
        } else {
            $is_active = 1;
        }

        if (isset($_GET['ap_year'])) {
            $ap_year = $_GET['ap_year'];
        } else {
            $ap_year = date('Y');
        }

        if (isset($_GET['ap_month'])) {

            $ap_month = $_GET['ap_month'];
        } else {
            $ap_month = date('m');
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

        $outputStrategy = ApRowStatusOutputStrategy::OUTPUT_IN_ARRAY;
        $total_records = $this->getApReporter()->getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);
        $a_json_final = array();

        if ($total_records > 0) {

            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
                $offset = $paginator->minInPage - 1;

                $result = $this->getApReporter()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy);
            } else {
                $result = $this->getApReporter()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy);
            }

            $a_json_final['data'] = $result;
            $a_json_final['totalRecords'] = $total_records;
            $a_json_final['curPage'] = $pq_curPage;
        }

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
