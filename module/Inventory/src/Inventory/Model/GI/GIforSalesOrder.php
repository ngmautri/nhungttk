<?php
namespace Inventory\Model\GI;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforRepair extends AbstractGIStrategy
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



  
}