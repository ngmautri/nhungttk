<?php
namespace Procure\Model\Domain\PurchaseRequest;

use Procure\Domain\APInvoice\APInvoiceId;
use Application\Domain\Shared\Currency;

/**
 * AP Invoice Aggregate.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoice
{

    /**
     *
     * @var APInvoiceId
     */
    private $id;

    /**
     *
     * @var String
     */
    private $vendorId;

    /**
     *
     * @var Currency
     */
    private $currency;

    private $invoiceNumber;

    private $exchangeRate;

    private $documentDate;

    private $postingDate;

    private $goodsReceiptDate;

    private $incoterm;

    private $incotermPlace;

    private $paymentTerm;

    private $paymentMethod;

    private $remarks;
}
