<?php
namespace Inventory\Model\Valuation\Strategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface InterfaceReceivingList
{
    public function add($itemGR);
    public function remove($itemGR);    
}