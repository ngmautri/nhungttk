<?php
namespace HR\Domain\Employee\LabourContract;

use HR\Domain\ValueObject\Employee\EmployeeCode;
use HR\Domain\ValueObject\Employee\ContractDuration;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseLabourContract extends AbstractLabourContract
{

    /**
     *
     * @var EmployeeCode $employeeCodeVO
     */
    protected $employeeCodeVO;

    /**
     *
     * @var ContractDuration $contractDuration
     */
    protected $contractDuration;

    protected $remunations;

    /**
     *
     * @return \HR\Domain\ValueObject\Employee\EmployeeCode
     */
    public function getEmployeeCodeVO()
    {
        return $this->employeeCodeVO;
    }

    /**
     *
     * @return \HR\Domain\ValueObject\Employee\ContractDuration
     */
    public function getContractDuration()
    {
        return $this->contractDuration;
    }

    /**
     *
     * @return mixed
     */
    public function getRemunations()
    {
        return $this->remunations;
    }
}