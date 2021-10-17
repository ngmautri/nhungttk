<?php
namespace Procure\Domain\Clearing;

/**
 * Clearing Row Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingRowSnapshot
{

    /*
     * |=============================
     * | Application\Entity\NmtProcureClearingRow
     * |
     * |=============================
     */
    public $id;

    public $token;

    public $rtRow;

    public $createdOn;

    public $clearingStandardQuantity;

    public $remarks;

    public $rowIdentifer;

    public $revisionNo;

    public $docVersion;

    public $lastChangeOn;

    public $doc;

    public $prRow;

    public $qoRow;

    public $poRow;

    public $grRow;

    public $apRow;

    public $createdBy;

    public $lastChangeBy;

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getRtRow()
    {
        return $this->rtRow;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getClearingStandardQuantity()
    {
        return $this->clearingStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     *
     * @return mixed
     */
    public function getQoRow()
    {
        return $this->qoRow;
    }

    /**
     *
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     *
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     *
     * @return mixed
     */
    public function getApRow()
    {
        return $this->apRow;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $rtRow
     */
    public function setRtRow($rtRow)
    {
        $this->rtRow = $rtRow;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $clearingStandardQuantity
     */
    public function setClearingStandardQuantity($clearingStandardQuantity)
    {
        $this->clearingStandardQuantity = $clearingStandardQuantity;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $docVersion
     */
    public function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $doc
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    /**
     *
     * @param mixed $prRow
     */
    public function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @param mixed $qoRow
     */
    public function setQoRow($qoRow)
    {
        $this->qoRow = $qoRow;
    }

    /**
     *
     * @param mixed $poRow
     */
    public function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    public function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $apRow
     */
    public function setApRow($apRow)
    {
        $this->apRow = $apRow;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }
}
