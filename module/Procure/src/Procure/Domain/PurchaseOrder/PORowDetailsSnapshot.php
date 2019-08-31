<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDetailsSnapshot extends PORowSnapshot
{

    public $draftGrQuantity;

    public $postedGrQuantity;

    public $confirmedGrBalance;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    public $billedAmount;
}