<?php
namespace Procure\Model\Domain\PurchaseRequest;

use Procure\Domain\APInvoice\APInvoiceId;
use Application\Domain\Shared\Quantity;
use Application\Domain\Shared\Money;

/**
 * AP Row Row.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceRow
{

    private $id;

    /**
     *
     * @var APInvoiceId
     */
    private $invoiceId;

    /**
     * string only for distributed solution
     *
     * @var string
     */
    private $itemId;

    /**
     *
     * @var Quantity;
     */
    private $quantity;

    /**
     *
     * @var Money;
     */
    private $unitPrice;
    
    
    /**
     *
     * @var PurchaseRequestId
     */
    private $purchaseRequestId;

    private $purchaseOrderId;

    private $quotationOrderId;
    
    
    /**
     * 
     * @var array
     */
    private $rows;
}
