<?php
namespace Inventory\Infrastructure\Persistance;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemReportingRepositoryInterface
{
    public function getAllItemWithSerial($itemId =null);
}
