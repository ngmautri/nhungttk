<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseDoc extends AbstractPR
{

    // Specific Attribute
    // ===================
    protected $prAutoNumber;

    protected $prNumber;

    protected $prName;

    protected $keywords;

    protected $status;

    protected $checksum;

    protected $submittedOn;

    protected $totalRowManual;

    protected $department;

    // addtional attribute.
    // =======================================
    protected $attachmentList;

    // ===================

    /**
     *
     * @return mixed
     */
    public function getPrAutoNumber()
    {
        return $this->prAutoNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrName()
    {
        return $this->prName;
    }

    /**
     *
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalRowManual()
    {
        return $this->totalRowManual;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     *
     * @param mixed $prAutoNumber
     */
    protected function setPrAutoNumber($prAutoNumber)
    {
        $this->prAutoNumber = $prAutoNumber;
    }

    /**
     *
     * @param mixed $prNumber
     */
    protected function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;
    }

    /**
     *
     * @param mixed $prName
     */
    protected function setPrName($prName)
    {
        $this->prName = $prName;
    }

    /**
     *
     * @param mixed $keywords
     */
    protected function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     *
     * @param mixed $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @param mixed $checksum
     */
    protected function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $submittedOn
     */
    protected function setSubmittedOn($submittedOn)
    {
        $this->submittedOn = $submittedOn;
    }

    /**
     *
     * @param mixed $totalRowManual
     */
    protected function setTotalRowManual($totalRowManual)
    {
        $this->totalRowManual = $totalRowManual;
    }

    /**
     *
     * @param mixed $department
     */
    protected function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     *
     * @return mixed
     */
    public function getAssociationList()
    {
        return $this->associationList;
    }

    /**
     *
     * @param mixed $associationList
     */
    public function setAssociationList($associationList)
    {
        $this->associationList = $associationList;
    }
}