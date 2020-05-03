<?php
namespace Inventory\Domain\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseDoc extends AbstractTrx
{

    // Specific Attribute, that are not on generic doc.
    // ===================
    protected $movementType;

    protected $movementDate;

    protected $journalMemo;

    protected $movementFlow;

    protected $movementTypeMemo;

    protected $reversalDoc;

    protected $isReversable;

    protected $isTransferTransaction;

    protected $targetWarehouse;

    protected $sourceLocation;

    protected $tartgetLocation;

    // ===================

    /**
     *
     * @param mixed $movementType
     */
    public function setMovementType($movementType)
    {
        $this->movementType = $movementType;
    }

    /**
     *
     * @param mixed $movementDate
     */
    public function setMovementDate($movementDate)
    {
        $this->movementDate = $movementDate;
    }

    /**
     *
     * @param mixed $journalMemo
     */
    public function setJournalMemo($journalMemo)
    {
        $this->journalMemo = $journalMemo;
    }

    /**
     *
     * @param mixed $movementFlow
     */
    public function setMovementFlow($movementFlow)
    {
        $this->movementFlow = $movementFlow;
    }

    /**
     *
     * @param mixed $movementTypeMemo
     */
    public function setMovementTypeMemo($movementTypeMemo)
    {
        $this->movementTypeMemo = $movementTypeMemo;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $isReversable
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @param mixed $isTransferTransaction
     */
    public function setIsTransferTransaction($isTransferTransaction)
    {
        $this->isTransferTransaction = $isTransferTransaction;
    }

    /**
     *
     * @param mixed $targetWarehouse
     */
    public function setTargetWarehouse($targetWarehouse)
    {
        $this->targetWarehouse = $targetWarehouse;
    }

    /**
     *
     * @param mixed $sourceLocation
     */
    public function setSourceLocation($sourceLocation)
    {
        $this->sourceLocation = $sourceLocation;
    }

    /**
     *
     * @param mixed $tartgetLocation
     */
    public function setTartgetLocation($tartgetLocation)
    {
        $this->tartgetLocation = $tartgetLocation;
    }

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