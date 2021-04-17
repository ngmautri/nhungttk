<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountSnapshot extends AbstractDTO
{

    public $id;

    public $uuid;

    public $token;

    public $accountNumer;

    public $accountName;

    public $accountType;

    public $accountClass;

    public $accountGroup;

    public $parentAccountNumber;

    public $isActive;

    public $description;

    public $createdOn;

    public $lastChangeOn;

    public $remarks;

    public $allowReconciliation;

    public $hasCostCenter;

    public $isClearingAccount;

    public $isControlAccount;

    public $manualPostingBlocked;

    public $allowPosting;

    public $coa;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountNumer()
    {
        return $this->accountNumer;
    }

    /**
     *
     * @param mixed $accountNumer
     */
    public function setAccountNumer($accountNumer)
    {
        $this->accountNumer = $accountNumer;
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
     * @param mixed $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
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
     * @param mixed $accountType
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
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
     * @param mixed $accountClass
     */
    public function setAccountClass($accountClass)
    {
        $this->accountClass = $accountClass;
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
     * @param mixed $accountGroup
     */
    public function setAccountGroup($accountGroup)
    {
        $this->accountGroup = $accountGroup;
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
     * @param mixed $parentAccountNumber
     */
    public function setParentAccountNumber($parentAccountNumber)
    {
        $this->parentAccountNumber = $parentAccountNumber;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
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
     * @param mixed $allowReconciliation
     */
    public function setAllowReconciliation($allowReconciliation)
    {
        $this->allowReconciliation = $allowReconciliation;
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
     * @param mixed $hasCostCenter
     */
    public function setHasCostCenter($hasCostCenter)
    {
        $this->hasCostCenter = $hasCostCenter;
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
     * @param mixed $isClearingAccount
     */
    public function setIsClearingAccount($isClearingAccount)
    {
        $this->isClearingAccount = $isClearingAccount;
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
     * @param mixed $isControlAccount
     */
    public function setIsControlAccount($isControlAccount)
    {
        $this->isControlAccount = $isControlAccount;
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
     * @param mixed $manualPostingBlocked
     */
    public function setManualPostingBlocked($manualPostingBlocked)
    {
        $this->manualPostingBlocked = $manualPostingBlocked;
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
     * @param mixed $allowPosting
     */
    public function setAllowPosting($allowPosting)
    {
        $this->allowPosting = $allowPosting;
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
     * @param mixed $coa
     */
    public function setCoa($coa)
    {
        $this->coa = $coa;
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
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }
}