<?php
namespace Inventory\Application\Reporting\ItemSerial;

use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Domain\Util\Collection\Render\DefaultRenderAsArray;
use Application\Domain\Util\Collection\Render\TestRenderAsParamQuery;
use Application\Domain\Util\Pagination\Paginator;
use Application\Service\AbstractService;
use Inventory\Application\Reporting\ItemSerial\CollectionRender\DefaultItemSerialRenderAsHtmlTable;
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

    public function getItemSerialCollectionRender($filter, $page, $resultPerPage = 10, $renderType = SupportedRenderType::HMTL_TABLE)
    {
        if (! $filter instanceof ItemSerialSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        // create Paginator
        $totalResults = $this->getListTotal($filter);
        $paginator = new Paginator($totalResults, $page, $resultPerPage);

        // var_dump($paginator);

        $f = "/inventory/item-serial/list1?target_id=%s&token=%s";
        $url = sprintf($f, $filter->getItemId(), "");
        $paginator->setBaseUrl($url);
        $paginator->setUrlConnectorSymbol("&");
        $paginator->setDisplayHTMLDiv("item_serial_div");

        $rep = new ItemSerialQueryRepositoryImpl($this->getDoctrineEM());

        // create collection

        $filter->setOffset($paginator->getOffset());
        $filter->setLimit($paginator->getLimit());

        $filter->setSortBy('createdDate');
        $filter->setSort('DESC');
        $collection = $rep->getList($filter);

        $render = null;
        switch ($renderType) {

            case SupportedRenderType::HMTL_TABLE:
                $render = new DefaultItemSerialRenderAsHtmlTable($totalResults, $collection);
                $render->setPaginator($paginator);
                break;

            case SupportedRenderType::PARAM_QUERY:
                $render = new TestRenderAsParamQuery($totalResults, $collection);
                break;

            case SupportedRenderType::AS_ARRAY:
                $render = new DefaultRenderAsArray($totalResults, $collection);
                break;

            default:
                $render = new TestRenderAsParamQuery($totalResults, $collection);
                break;
        }

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
