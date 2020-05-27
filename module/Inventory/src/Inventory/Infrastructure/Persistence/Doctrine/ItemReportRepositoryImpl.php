<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Contract\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Filter\ItemSerialSqlFilter;
use Inventory\Infrastructure\Persistence\Helper\ItemReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportRepositoryImpl extends AbstractDoctrineRepository implements ItemReportRepositoryInterface
{

    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0)
    {}

    public function getLastCreatedItems($limit = 100, $offset = 0)
    {}

    public function getAllItemWithSerial($itemId = null)
    {
        $filter = new ItemSerialSqlFilter();
        $filter->setItemId($itemId);
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;

        $results = ItemReportHelper::getItemListWithSerialNumber($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        echo count($results);
        foreach ($results as $r) {
            foreach ($r[0]->getSerials() as $s) {
                echo $s->getId() . "\n";
            }

            foreach ($r[0]->getPictures() as $s) {
                echo "pic" . $s->getId() . "\n";
            }

            foreach ($r[0]->getAttachments() as $s) {
                echo "attachments: " . $s->getFilenameOriginal() . "\n";
            }

            foreach ($r[0]->getPrList() as $s) {
                echo "Pr:" . $s->getId() . "\n";
            }

            foreach ($r[0]->getPoList() as $s) {
                echo "Po:" . $s->getId() . "\n";
            }

            foreach ($r[0]->getApList() as $s) {
                echo "AP:" . $s->getInvoice()->getId() . "\n";
            }

            foreach ($r[0]->getFifoLayerList() as $s) {
                echo "Fifo:" . $s->getId() . "\n";
            }
        }
    }

    public function get($id, $token)
    {
        return ItemReportHelper::getItem($this->getDoctrineEM(), $id, $token);
    }

    public function getLastAPRows($limit = 100, $offset = 0)
    {}

    public function getOnhandInWahrehouse($itemId, $wareHouseId, $transactionDate)
    {}

    public function getRandomItem()
    {}

    public function getMostOrderItems($limit = 50, $offset = 0)
    {}

    public function getLastCreatedPrRow($limit = 100, $offset = 0)
    {}

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}

    public function getListTotal(SqlFilterInterface $filter)
    {}
}
