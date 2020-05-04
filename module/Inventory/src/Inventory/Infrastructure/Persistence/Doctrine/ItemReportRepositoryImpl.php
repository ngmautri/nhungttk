<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Persistence\ItemReportRepositoryInterface;
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
    {}

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
}
