<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Inventory\Application\Export\Item\Contracts\SaveAsSupportedType;
use Inventory\Application\Reporting\Item\ItemReporter;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
use MLA\Paginator;
use Zend\View\Model\ViewModel;

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

    // ====================================
    // Setter and Getter.
    // ====================================

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
