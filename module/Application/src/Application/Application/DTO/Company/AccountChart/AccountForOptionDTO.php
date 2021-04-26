<?php
namespace Application\Application\DTO\Company\AccountChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class AccountForOptionDTO
{

    public $accountShowName;

    public $accountName;

    public $accountCode;

    /**
     *
     * @return mixed
     */
    public function getAccountShowName()
    {
        return $this->accountShowName;
    }

    /**
     *
     * @param mixed $accountShowName
     */
    public function setAccountShowName($accountShowName)
    {
        $this->accountShowName = $accountShowName;
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
    public function getAccountCode()
    {
        return $this->accountCode;
    }

    /**
     *
     * @param mixed $accountCode
     */
    public function setAccountCode($accountCode)
    {
        $this->accountCode = $accountCode;
    }
}
