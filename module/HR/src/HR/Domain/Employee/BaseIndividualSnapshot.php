<?php
namespace HR\Domain\Employee;

use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseIndividualSnapshot extends IndividualSnapshot
{

    public $employeeCodeVO;

    public $workingAgeVO;

    public $personNameVO;

    public $birthdayVO;

    public $genderVO;

    public function init($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);

        $this->setRevisionNo(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->uuid);
    }

    /**
     *
     * @return mixed
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
     * @return mixed
     */
    public function getPersonNameVO()
    {
        return $this->personNameVO;
    }

    /**
     *
     * @return mixed
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