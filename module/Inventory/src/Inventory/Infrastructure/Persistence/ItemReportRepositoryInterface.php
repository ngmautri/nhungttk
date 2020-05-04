<?php
namespace Inventory\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemReportRepositoryInterface
{

    public function get($id, $token);

    public function getAllItemWithSerial($itemId = null);

    public function getMostOrderItems($limit = 50, $offset = 0);

    public function getLastCreatedItems($limit = 100, $offset = 0);

    public function getRandomItem();

    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0);

    public function getLastAPRows($limit = 100, $offset = 0);

    public function getLastCreatedPrRow($limit = 100, $offset = 0);

    public function getOnhandInWahrehouse($itemId, $wareHouseId, $transactionDate);
}
