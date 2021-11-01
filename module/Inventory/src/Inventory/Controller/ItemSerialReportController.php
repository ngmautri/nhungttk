<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Util\Pagination\Paginator;
use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialReportController extends AbstractGenericController
{

    protected $prReporter;

    public function ofItemAction()
    {
        // $this->layout("layout/fluid");
        $this->layout("layout/user/ajax");

        $file_type = (int) $this->params()->fromQuery('file_type');
        $itemId = (int) $this->params()->fromQuery('item_id');

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

        $paginator = null;
        $result = null;

        $limit = null;
        $offset = null;
        $total_records = null;

        $filter = new PrRowReportSqlFilter();
        $filter->setItemId($itemId); // important
        $filter->setBalance(100);
        $filter->setSortBy('prSubmitted');
        $filter->setSort(SqlKeyWords::DESC);

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);
        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            return $this->getPrReporter()->getAllRow($filter, $file_type);
        }

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();

            $filter->setLimit($limit);
            $filter->setOffset($offset);
        }

        if (! $file_type == SaveAsSupportedType::OUTPUT_IN_ARRAY) {
            $result = $this->getPrReporter()->getAllRow($filter, $file_type);
        } else {
            $result = null;
        }

        return new ViewModel(array(
            'file_type' => $file_type,
            'item_id' => $itemId,
            'result' => $result,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function ofItemGirdAction()
    {
        if (isset($_GET['item_id'])) {
            $itemId = (int) $_GET['item_id'];
        } else {
            $itemId = 0;
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

        $filter = new PrRowReportSqlFilter();
        $filter->setItemId($itemId); // important
        $filter->setBalance(100);
        $filter->setSortBy('prSubmitted');
        $filter->setSort(SqlKeyWords::DESC);

        $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

        $total_records = $this->getPrReporter()->getAllRowTotal($filter);

        $a_json_final = array();

        if ($total_records > 0) {
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);

                $limit = $paginator->getLimit();
                $offset = $paginator->getOffset();
                $filter->setLimit($limit);
                $filter->setOffset($offset);
            }
        }

        $result = $this->getPrReporter()->getAllRow($filter, $file_type);

        $a_json_final['data'] = $result;
        $a_json_final['totalRecords'] = $total_records;
        $a_json_final['curPage'] = $pq_curPage;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /*
     * |=============================
     * | Getter and Setter
     * |
     * |=============================
     */
}
