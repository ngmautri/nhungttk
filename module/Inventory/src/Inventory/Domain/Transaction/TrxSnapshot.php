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

    /**
     *
     * @return mixed
     */
    public function getMovementType()
    {
        return $this->movementType;
    }

    /**
     *
     * @return mixed
     */
    public function getMovementDate()
    {
        return $this->movementDate;
    }

    /**
     *
     * @return mixed
     */
    public function getJournalMemo()
    {
        return $this->journalMemo;
    }

    /**
     *
     * @return mixed
     */
    public function getMovementFlow()
    {
        return $this->movementFlow;
    }

    /**
     *
     * @return mixed
     */
    public function getMovementTypeMemo()
    {
        return $this->movementTypeMemo;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     *
     * @return mixed
     */
    public function getIsTransferTransaction()
    {
        return $this->isTransferTransaction;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetWarehouse()
    {
        return $this->targetWarehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getTartgetLocation()
    {
        return $this->tartgetLocation;
    }
}