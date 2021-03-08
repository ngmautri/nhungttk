<?php
namespace HR\Domain\Employee;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Date\Birthday;
use Application\Domain\Shared\Person\PersonName;
use Application\Domain\Shared\Person\WorkingAge;
use HR\Domain\ValueObject\Employee\EmployeeCode;
use Ramsey\Uuid\Uuid;
use HR\Domain\Validator\Employee\IndividualValidatorCollection;
use Application\Domain\Shared\Person\Gender;

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

    protected $birthdayVO;

    protected $genderVO;

    // =========================
    public function init($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);

        $this->setRevisionNo(0);
        $this->setVersion(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    /**
     *
     * @param IndividualValidatorCollection $validators
     */
    public function validate(IndividualValidatorCollection $validators)
    {
        $validators->validate($this);
    }

    // ======================
    public abstract function createVO();

    public abstract function specify();

    protected function createGenderVO()
    {
        try {
            $this->genderVO = new Gender($this->getGender());
        } catch (\InvalidArgumentException $e) {
            $this->addError($e->getMessage());
        }
    }

    protected function createEmployeeCodeVO()
    {
        try {
            $this->employeeCodeVO = new EmployeeCode($this->getEmployeeCode());
        } catch (\InvalidArgumentException $e) {
            $this->addError($e->getMessage());
        }
    }

    protected function createBirthDayVO()
    {
        try {
            $this->birthdayVO = new Birthday($this->getBirthday());
        } catch (\InvalidArgumentException $e) {
            $this->addError($e->getMessage());
        }
    }

    protected function createWorkingAgeVO()
    {
        try {
            $birthday = new Birthday($this->getBirthday());
            $this->birthdayVO = $birthday;
            $this->workingAgeVO = new WorkingAge($birthday);
        } catch (\InvalidArgumentException $e) {
            $this->addError($e->getMessage());
        }
    }

    protected function createPersonNameVO()
    {
        try {
            $this->personNameVO = new PersonName($this->getFirstName(), $this->getMiddleName(), $this->getLastName());
        } catch (\InvalidArgumentException $e) {
            $this->addError($e->getMessage());
        }
    }

    public function makeSnapshot()
    {
        $rootSnapshot = GenericObjectAssembler::updateAllFieldsFrom(new BaseIndividualSnapshot(), $this);

        if (! $rootSnapshot instanceof BaseIndividualSnapshot) {
            throw new \InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
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

    /**
     *
     * @return \Application\Domain\Shared\Date\Birthday
     */
    public function getBirthdayVO()
    {
        return $this->birthdayVO;
    }

    /**
     *
     * @return mixed
     */
    public function getGenderVO()
    {
        return $this->genderVO;
    }
}