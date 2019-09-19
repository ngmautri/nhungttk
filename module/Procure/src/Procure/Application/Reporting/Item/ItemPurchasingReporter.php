<?php
namespace Procure\Application\Reporting\Item;

use Application\Service\AbstractService;
use Procure\Infrastructure\Persistence\Doctrine\ItemReportRepostitoryImpl;
use Procure\Application\Reporting\Item\Output\ItemPriceCompareOutputStrategy;
use Procure\Application\Reporting\Item\Output\ItemPriceCompareInArray;
use Procure\Application\Reporting\Item\Output\ItemPriceCompareInHTMLTable;

/**
 * ItemPurchasingReporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemPurchasingReporter extends AbstractService
{

    /**
     *
     * @param int $itemId
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @param int $outputStrategy
     */
    public function getItemPriceComparision($itemId, $sort_by, $sort, $limit, $offset, $outputStrategy)
    {
        $rep = new ItemReportRepostitoryImpl($this->getDoctrineEM());
        $result = $rep->getPriceOfItem($itemId);

        if (count($result) == 0) {
            return null;
        }

        $factory = null;

        switch ($outputStrategy) {

            case ItemPriceCompareOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new ItemPriceCompareInArray();
                break;

            case ItemPriceCompareOutputStrategy::OUTPUT_IN_HMTL_TABLE:
                $factory = new ItemPriceCompareInHTMLTable();
                break;

            default:
                $factory = new ItemPriceCompareInHTMLTable();
                break;
        }

        $output = $factory->createOutput($result);

        return $output;
    }
}
