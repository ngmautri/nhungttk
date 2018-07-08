<?php
namespace Inventory\Model\Valuation\Strategy;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractValuationMethod
{
    abstract public function doValuation($receivingList);
}