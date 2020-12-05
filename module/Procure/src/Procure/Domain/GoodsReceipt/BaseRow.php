<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseRow extends GenericRow
{

    // Specific Attributes
    // =================================
    protected $grDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $flow;

    protected $gr;

    protected $apInvoiceRow;

    protected $poRow;

    protected $poId;

    protected $poToken;

    protected $apId;

    protected $apToken;
}
