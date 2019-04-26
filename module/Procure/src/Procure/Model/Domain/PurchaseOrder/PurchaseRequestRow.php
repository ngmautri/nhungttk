<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 * Purchase Request Row.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PurchaseRequestRow
{

    private $id;

    private $itemId;

    private $quantity;

    private $unit;

    private $convertFactor;

    private $unitPrice;
    
    private $currency;
    
    private $edt;
}
