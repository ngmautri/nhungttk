<?php
namespace Inventory\Domain\Transaction;

use Procure\Domain\DocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxSnapshot extends DocSnapshot
{

    public $movementType;

    public $movementDate;

    public $journalMemo;

    public $movementFlow;

    public $movementTypeMemo;

    public $reversalDoc;

    public $isReversable;

    public $isTransferTransaction;

    public $targetWarehouse;

    public $sourceLocation;

    public $tartgetLocation;
}