<?php
namespace Inventory\Application\Reporting\ItemSerial;

use Application\Application\Helper\Form\FormHelper;
use Application\Application\Helper\Form\FormHelperConst;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Domain\Util\Collection\Render\DefaultRenderAsArray;
use Application\Domain\Util\Pagination\Paginator;
use Application\Service\AbstractService;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\DefaultItemSerialRenderAsHtmlTableWithForm;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\ItemSerialRenderAsExcel;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\ItemSerialRenderAsParamQuery;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\Spreadsheet\ExcelBuilder;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\Spreadsheet\OpenOfficeBuilder;
use Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\ItemSerialQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;

/**
 * Item Serial Report Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialReporter extends AbstractService
{

    private $reporterRespository;

    /**
     *
     * @param object $filter
     * @param int $page
     * @param int $resultPerPage
     * @param string $renderType
     * @throws \InvalidArgumentException
     * @return NULL|\Application\Domain\Util\Collection\Render\TestRenderAsParamQuery
     */
    public function getItemSerialCollectionRender($filter, $page, $resultPerPage = 10, $renderType = SupportedRenderType::HMTL_TABLE)

    {
        if (! $filter instanceof ItemSerialSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        // create Paginator
        $totalResults = $this->getListTotal($filter);

        if ($totalResults == null or $totalResults == 0) {
            return null;
        }

        $paginator = new Paginator($totalResults, $page, $resultPerPage);

        $f = "/inventory/item-serial/list1?itemId=%s&token=%s&render_type=%s&docMonth=%s";
        $url = sprintf($f, $filter->getItemId(), "", $renderType, $filter->getDocMonth());
        $paginator->setBaseUrl($url);
        $paginator->setUrlConnectorSymbol("&");
        $paginator->setDisplayHTMLDiv("item_serial_div");

        $rep = new ItemSerialQueryRepositoryImpl($this->getDoctrineEM());

        // create collection
        $filter->setOffset($paginator->getOffset());
        $filter->setLimit($paginator->getLimit());

        if ($renderType == SupportedRenderType::EXCEL || $renderType == SupportedRenderType::OPEN_OFFICE) {
            $filter->setOffset(null);
            $filter->setLimit(null);
        }

        $filter->setSortBy('createdDate');
        $filter->setSort('DESC');
        $collection = $rep->getList($filter);

        $format = "/inventory/item-serial/list1?itemId=%s&render_type=%s";
        $excel_url = sprintf($format, $filter->getItemId(), SupportedRenderType::EXCEL);
        $oo_url = sprintf($format, $filter->getItemId(), SupportedRenderType::OPEN_OFFICE);
        $table_html_url = sprintf($format, $filter->getItemId(), SupportedRenderType::HMTL_TABLE);
        $param_query_url = sprintf($format, $filter->getItemId(), SupportedRenderType::PARAM_QUERY);

        $param_onclick = \sprintf("doPaginatorV1('%s','%s')", $table_html_url, 'item_serial_div');
        $html_onclick = \sprintf("doPaginatorV1('%s','%s')", $param_query_url, 'item_serial_div');

        $list = [
            FormHelper::createLink($excel_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Excel (*.xlxs)'),
            FormHelperConst::DIVIDER,
            FormHelper::createLink($oo_url, '<i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Open Office (*.ods)')
        ];

        $render = null;
        $toolbar1 = '';
        switch ($renderType) {

            case SupportedRenderType::HMTL_TABLE:
                $render = new DefaultItemSerialRenderAsHtmlTableWithForm($totalResults, $collection);
                $render->setPaginator($paginator);
                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-th" aria-hidden="true"></i>', $html_onclick, 'Gird View');

                break;

            case SupportedRenderType::PARAM_QUERY:

                $format = '/inventory/item-serial/gird?itemId=%s&docMonth=%s&pq_rpp=%s';
                $remoteUrl = sprintf($format, $filter->getItemId(), $filter->getDocMonth(), $resultPerPage);

                $render = new ItemSerialRenderAsParamQuery($totalResults, $collection);
                $render->setRemoteUrl($remoteUrl);
                $render->setPaginator($paginator);
                $toolbar1 = $toolbar1 . FormHelper::createButtonForJS('<i class="fa fa-list" aria-hidden="true"></i>', $param_onclick, 'Table View');

                break;

            case SupportedRenderType::EXCEL:

                $render = new ItemSerialRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new ExcelBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::OPEN_OFFICE:

                $render = new ItemSerialRenderAsExcel($totalResults, $collection);
                $render->setBuilder(new OpenOfficeBuilder());
                $render->execute(); // render immediately.

                break;

            case SupportedRenderType::AS_ARRAY:
                $render = new DefaultRenderAsArray($totalResults, $collection);
                $render->setPaginator($paginator);
                break;

            default:
                $render = new DefaultItemSerialRenderAsHtmlTableWithForm($totalResults, $collection);
                $render->setPaginator($paginator);
                break;
        }

        $toolbar = FormHelper::createDropDownBtn($list, '', $toolbar1);

        $render->setToolbar($toolbar);

        return $render;
    }

    /**
     *
     * @param unknown $filter
     * @param unknown $file_type
     * @throws \InvalidArgumentException
     * @return NULL|NULL[]
     */
    public function getListTotal(ItemSerialSqlFilter $filter)
    {
        $key = \sprintf("_item_serial_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {

            $rep = new ItemSerialQueryRepositoryImpl($this->getDoctrineEM());
            $total = $rep->getListTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }

        return $total;
    }

    /*
     * |=============================
     * |Getter and Setter
     * |
     * |=============================
     */

    /**
     *
     * @param ItemSerialQueryRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(ItemSerialQueryRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }
}
