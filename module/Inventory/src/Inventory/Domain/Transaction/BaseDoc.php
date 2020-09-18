<?php
namespace Inventory\Domain\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseDoc extends AbstractTrx
{

    // Specific Attribute, that are not on generic doc.
    protected $unusedRows;

    protected $exhaustedRows;

    protected $zeroQtyRows;

    // ===================
    protected $lazyRowSnapshotCollection;

    protected $lazyRowSnapshotCollectionReference;

    // /========
    protected $rowsCollectionReference;

    protected $rowsCollection;

    protected $rowsReference;

    protected $rawRows;

    protected $lazyDocRows;

    // Entity MV ========
    protected $movementType;

    protected $movementDate;

    protected $journalMemo;

    protected $movementFlow;

    protected $movementTypeMemo;

    protected $reversalDoc;

    protected $isReversable;

    protected $isTransferTransaction;

    protected $relevantMovementId;

    protected $targetWarehouse;

    protected $sourceLocation;

    protected $tartgetLocation;

    /**
     *
     * @return mixed
     */
    public function getLazyRowSnapshotCollection()
    {
        $ref = $this->getLazyRowSnapshotCollectionReference();
        $this->lazyRowSnapshotCollection = $ref();
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
     * @param mixed $lazyRowSnapshotCollection
     */
    public function setLazyRowSnapshotCollection($lazyRowSnapshotCollection)
    {
        $this->lazyRowSnapshotCollection = $lazyRowSnapshotCollection;
    }

    /**
     *
     * @param mixed $lazyRowSnapshotCollectionReference
     */
    public function setLazyRowSnapshotCollectionReference($lazyRowSnapshotCollectionReference)
    {
        $this->lazyRowSnapshotCollectionReference = $lazyRowSnapshotCollectionReference;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsSnapshotCollectionReference()
    {
        return $this->rowsSnapshotCollectionReference;
    }

    /**
     *
     * @param mixed $rowsSnapshotCollectionReference
     */
    public function setRowsSnapshotCollectionReference($rowsSnapshotCollectionReference)
    {
        $this->rowsSnapshotCollectionReference = $rowsSnapshotCollectionReference;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRowsCollection()
    {
        if ($this->rowsCollection == null) {
            $this->rowsCollection = new ArrayCollection();
            return ($this->rowsCollection);
        }

        return $this->rowsCollection;
    }

    /**
     *
     * @param TrxRow $row
     */
    public function addIntoRowsCollection(TrxRow $row)
    {
        $collection = $this->getRowsCollection();
        $collection->add($row);
    }

    /**
     *
     * @param ArrayCollection $rowsCollection
     */
    public function setRowsCollection(ArrayCollection $rowsCollection)
    {
        $this->rowsCollection = $rowsCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getLazyDocRows()
    {
        $rowRef = $this->getRowsReference();
        $this->docRows = $rowRef();
        return $this->docRows;
    }

    public function getLazyRowsCollection()
    {
        $rowRef = $this->getRowsCollectionReference();
        $this->rowsCollection = $rowRef();
        return $this->rowsCollection;
    }

    /**
     *
     * @param mixed $lazyDocRows
     */
    public function setLazyDocRows($lazyDocRows)
    {
        $this->lazyDocRows = $lazyDocRows;
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
     * @param mixed $rawRows
     */
    public function setRawRows($rawRows)
    {
        $this->rawRows = $rawRows;
    }

    /**
     *
     * @return Closure
     */
    public function getRowsReference()
    {
        return $this->rowsReference;
    }

    /**
     *
     * @param Closure $rowsReference
     */
    public function setRowsReference(Closure $rowsReference)
    {
        $this->rowsReference = $rowsReference;
    }

    /**
     *
     * @return Closure
     */
    public function getRowsCollectionReference()
    {
        return $this->rowsCollectionReference;
    }

    /**
     *
     * @param Closure $rowsCollectionReference
     */
    public function setRowsCollectionReference(Closure $rowsCollectionReference)
    {
        $this->rowsCollectionReference = $rowsCollectionReference;
    }

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
     * @param mixed $unusedRows
     */
    public function setUnusedRows($unusedRows)
    {
        $this->unusedRows = $unusedRows;
    }

    /**
     *
     * @param mixed $exhaustedRows
     */
    public function setExhaustedRows($exhaustedRows)
    {
        $this->exhaustedRows = $exhaustedRows;
    }

    /**
     *
     * @param mixed $zeroQtyRows
     */
    public function setZeroQtyRows($zeroQtyRows)
    {
        $this->zeroQtyRows = $zeroQtyRows;
    }

    /**
     *
     * @param mixed $relevantMovementId
     */
    protected function setRelevantMovementId($relevantMovementId)
    {
        $this->relevantMovementId = $relevantMovementId;
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