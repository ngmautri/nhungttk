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

    public $unusedRows;

    public $exhaustedRows;

    public $zeroQtyRows;

    public $lazyRowSnapshotCollection;

    public $lazyRowSnapshotCollectionReference;

    public $rowsCollectionReference;

    public $rowsCollection;

    public $rowsReference;

    public $rawRows;

    public $lazyDocRows;

    public $movementType;

    public $movementDate;

    public $journalMemo;

    public $movementFlow;

    public $movementTypeMemo;

    public $reversalDoc;

    public $isReversable;

    public $isTransferTransaction;

    public $relevantMovementId;

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

    /**
     *
     * @return mixed
     */
    public function getUnusedRows()
    {
        return $this->unusedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getExhaustedRows()
    {
        return $this->exhaustedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getZeroQtyRows()
    {
        return $this->zeroQtyRows;
    }

    /**
     *
     * @return mixed
     */
    public function getLazyRowSnapshotCollection()
    {
        return $this->lazyRowSnapshotCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getLazyRowSnapshotCollectionReference()
    {
        return $this->lazyRowSnapshotCollectionReference;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsCollectionReference()
    {
        return $this->rowsCollectionReference;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsCollection()
    {
        return $this->rowsCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsReference()
    {
        return $this->rowsReference;
    }

    /**
     *
     * @return mixed
     */
    public function getRawRows()
    {
        return $this->rawRows;
    }

    /**
     *
     * @return mixed
     */
    public function getLazyDocRows()
    {
        return $this->lazyDocRows;
    }

    /**
     *
     * @return mixed
     */
    public function getRelevantMovementId()
    {
        return $this->relevantMovementId;
    }
}