<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Domain\DocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRSnapshot extends DocSnapshot
{

    public $prAutoNumber;

    public $prNumber;

    public $prName;

    public $keywords;

    public $status;

    public $checksum;

    public $submittedOn;

    public $totalRowManual;

    public $department;

    public $attachmentList;

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
     * @return mixed
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }
}