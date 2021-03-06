<?php
namespace Inventory\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemReportRepositoryInterface
{

    public function getItemList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getItemListTotal(SqlFilterInterface $filter);

    public function getAllItemWithSerial($itemId = null);

    public function getMostOrderItems($limit = 50, $offset = 0);

    public function getLastCreatedItems($limit = 100, $offset = 0);

    public function getRandomItem();

    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0);

    public function getLastAPRows($limit = 100, $offset = 0);

    public function getLastCreatedPrRow($limit = 100, $offset = 0);

    public function getOnhandInWahrehouse($itemId, $wareHouseId, $transactionDate);
}
