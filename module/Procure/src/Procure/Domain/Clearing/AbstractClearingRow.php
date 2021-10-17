<?php
namespace Procure\Domain\Clearing;

use Application\Domain\Shared\AbstractEntity;

/**
 * Abstract Clearing Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractClearingRow extends AbstractEntity
{

    protected $id;

    protected $token;

    protected $rtRow;

    protected $createdOn;

    protected $clearingStandardQuantity;

    protected $remarks;

    protected $doc;

    protected $prRow;

    protected $qoRow;

    protected $poRow;

    protected $grRow;

    protected $apRow;

    protected $createdBy;

    protected $rowIdentifer;

    protected $revisionNo;

    protected $docVersion;

    protected $lastChangeOn;

    protected $lastChangeBy;

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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    protected function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $docVersion
     */
    protected function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $rtRow
     */
    protected function setRtRow($rtRow)
    {
        $this->rtRow = $rtRow;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $clearingStandardQuantity
     */
    protected function setClearingStandardQuantity($clearingStandardQuantity)
    {
        $this->clearingStandardQuantity = $clearingStandardQuantity;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $doc
     */
    protected function setDoc($doc)
    {
        $this->doc = $doc;
    }

    /**
     *
     * @param mixed $prRow
     */
    protected function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @param mixed $qoRow
     */
    protected function setQoRow($qoRow)
    {
        $this->qoRow = $qoRow;
    }

    /**
     *
     * @param mixed $poRow
     */
    protected function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    protected function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $apRow
     */
    protected function setApRow($apRow)
    {
        $this->apRow = $apRow;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

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
}
