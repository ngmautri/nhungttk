<?php
namespace Inventory\Model\GI;

use Inventory\Model\AbstractTransactionStrategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepair extends AbstractTransactionStrategy
{

    public function getTransactionIdentifer()
    {
        return "NO NAME";
    }

    public function execute()
    {}

    public function doPosting($entity, $u)
    {}

    public function validateRow($entity)
    {}

    public function check($trx, $item, $u)
    {}

    public function reverse($entity, $u, $reversalDate)
    {}

    public function getTransactionIdentifer()
    {}

    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null)
    {}

    public function getFlow()
    {}
}