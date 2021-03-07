<?php
namespace HR\Domain\Employee;

use HR\Domain\ValueObject\Employee\EmployeeCode;
use Application\Domain\Shared\Person\WorkingAge;
use Application\Domain\Shared\Date\Birthday;
use Application\Domain\Shared\Person\PersonName;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseIndividual extends AbstractIndividual
{

    // Vavlue Object
    protected $employeeCodeVO;

    protected $workingAgeVO;

    protected $personNameVO;

    // ======================
    protected abstract function createVO();

    protected function createEmployeeCodeVO()
    {
        // UOM VO
        // ==================
        $this->employeeCodeVO = new EmployeeCode($this->getEmployeeCode());
    }

    protected function createWorkingAgeVO()
    {
        // UOM VO
        // ==================
        $this->createWorkingAgeVO = new WorkingAge(new Birthday($this->getBirthday()));
    }

    protected function createPersonNameVO()
    {
        // UOM VO
        // ==================
        $this->personNameVO = new PersonName($this->getFirstName(), $this->getMiddleName(), $this->getLastName());
    }

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
     * @return mixed
     */
    public function getWorkingAgeVO()
    {
        return $this->workingAgeVO;
    }

    /**
     *
     * @return \Application\Domain\Shared\Person\PersonName
     */
    public function getPersonNameVO()
    {
        return $this->personNameVO;
    }
}