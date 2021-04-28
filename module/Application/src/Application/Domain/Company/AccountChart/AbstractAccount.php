<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractAccount extends AbstractEntity
{

    protected $id;

    protected $uuid;

    protected $token;

    protected $accountNumber;

    protected $accountName;

    protected $accountType;

    protected $accountClass;

    protected $accountGroup;

    protected $accountFeature;

    protected $parentAccountNumber;

    protected $isActive;

    protected $description;

    protected $createdOn;

    protected $lastChangeOn;

    protected $remarks;

    protected $allowReconciliation;

    protected $hasCostCenter;

    protected $isClearingAccount;

    protected $isControlAccount;

    protected $manualPostingBlocked;

    protected $allowPosting;

    protected $controlFor;

    protected $version;

    protected $revisionno;

    protected $coaUuid;

    protected $coa;

    protected $createdBy;

    protected $lastChangeBy;

    protected $accountName1;

    protected $accountName2;

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
    public function getUuid()
    {
        return $this->uuid;
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
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountClass()
    {
        return $this->accountClass;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountGroup()
    {
        return $this->accountGroup;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountFeature()
    {
        return $this->accountFeature;
    }

    /**
     *
     * @return mixed
     */
    public function getParentAccountNumber()
    {
        return $this->parentAccountNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getAllowReconciliation()
    {
        return $this->allowReconciliation;
    }

    /**
     *
     * @return mixed
     */
    public function getHasCostCenter()
    {
        return $this->hasCostCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getIsClearingAccount()
    {
        return $this->isClearingAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getIsControlAccount()
    {
        return $this->isControlAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getManualPostingBlocked()
    {
        return $this->manualPostingBlocked;
    }

    /**
     *
     * @return mixed
     */
    public function getAllowPosting()
    {
        return $this->allowPosting;
    }

    /**
     *
     * @return mixed
     */
    public function getControlFor()
    {
        return $this->controlFor;
    }

    /**
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionno()
    {
        return $this->revisionno;
    }

    /**
     *
     * @return mixed
     */
    public function getCoaUuid()
    {
        return $this->coaUuid;
    }

    /**
     *
     * @return mixed
     */
    public function getCoa()
    {
        return $this->coa;
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
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param mixed $accountNumber
     */
    protected function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     *
     * @param mixed $accountName
     */
    protected function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

    /**
     *
     * @param mixed $accountType
     */
    protected function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    /**
     *
     * @param mixed $accountClass
     */
    protected function setAccountClass($accountClass)
    {
        $this->accountClass = $accountClass;
    }

    /**
     *
     * @param mixed $accountGroup
     */
    protected function setAccountGroup($accountGroup)
    {
        $this->accountGroup = $accountGroup;
    }

    /**
     *
     * @param mixed $accountFeature
     */
    protected function setAccountFeature($accountFeature)
    {
        $this->accountFeature = $accountFeature;
    }

    /**
     *
     * @param mixed $parentAccountNumber
     */
    protected function setParentAccountNumber($parentAccountNumber)
    {
        $this->parentAccountNumber = $parentAccountNumber;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $description
     */
    protected function setDescription($description)
    {
        $this->description = $description;
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
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $allowReconciliation
     */
    protected function setAllowReconciliation($allowReconciliation)
    {
        $this->allowReconciliation = $allowReconciliation;
    }

    /**
     *
     * @param mixed $hasCostCenter
     */
    protected function setHasCostCenter($hasCostCenter)
    {
        $this->hasCostCenter = $hasCostCenter;
    }

    /**
     *
     * @param mixed $isClearingAccount
     */
    protected function setIsClearingAccount($isClearingAccount)
    {
        $this->isClearingAccount = $isClearingAccount;
    }

    /**
     *
     * @param mixed $isControlAccount
     */
    protected function setIsControlAccount($isControlAccount)
    {
        $this->isControlAccount = $isControlAccount;
    }

    /**
     *
     * @param mixed $manualPostingBlocked
     */
    protected function setManualPostingBlocked($manualPostingBlocked)
    {
        $this->manualPostingBlocked = $manualPostingBlocked;
    }

    /**
     *
     * @param mixed $allowPosting
     */
    protected function setAllowPosting($allowPosting)
    {
        $this->allowPosting = $allowPosting;
    }

    /**
     *
     * @param mixed $controlFor
     */
    protected function setControlFor($controlFor)
    {
        $this->controlFor = $controlFor;
    }

    /**
     *
     * @param mixed $version
     */
    protected function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     *
     * @param mixed $revisionno
     */
    protected function setRevisionno($revisionno)
    {
        $this->revisionno = $revisionno;
    }

    /**
     *
     * @param mixed $coaUuid
     */
    protected function setCoaUuid($coaUuid)
    {
        $this->coaUuid = $coaUuid;
    }

    /**
     *
     * @param mixed $coa
     */
    protected function setCoa($coa)
    {
        $this->coa = $coa;
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
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountName1()
    {
        return $this->accountName1;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountName2()
    {
        return $this->accountName2;
    }

    /**
     *
     * @param mixed $accountName1
     */
    protected function setAccountName1($accountName1)
    {
        $this->accountName1 = $accountName1;
    }

    /**
     *
     * @param mixed $accountName2
     */
    protected function setAccountName2($accountName2)
    {
        $this->accountName2 = $accountName2;
    }
}
