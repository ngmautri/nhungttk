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
        $isActive = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $file_type = $this->params()->fromQuery('file_type');

        $item_type = $this->params()->fromQuery('item_type');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');

        $layout = $this->params()->fromQuery('layout');
        $page = $this->params()->fromQuery('page');
        $resultsPerPage = $this->params()->fromQuery('perPage');

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
        $filter->setIsFixedAsset($is_fixed_asset);
        $filter->setItemType($item_type);

        $total_records = $this->getReporter()->getListTotal($filter);

        $limit = null;
        $offset = null;
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE) {
            $list = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);
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
            'perPage' => $resultsPerPage,
            'file_type' => $file_type,
            'is_fixed_asset' => $is_fixed_asset,
            'item_type' => $item_type,
            'layout' => $layout,
            'page' => $page
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

        if (isset($_GET['is_active'])) {
            $isActive = (int) $_GET['is_active'];
        } else {
            $isActive = 1;
        }

        if (isset($_GET['item_type'])) {
            $item_type = (int) $_GET['item_type'];
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
        $filter->setIsFixedAsset($isActive);
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

        $result = $this->getReporter()->getList($filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records);

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
