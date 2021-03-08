<?php
namespace HR\Domain\Employee;

use HR\Domain\Contracts\IndividualType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseEmployee extends BaseIndividual
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\BaseIndividual::createVO()
     */
    public function createVO()
    {
        $this->createPersonNameVO();
        $this->createGenderVO();
        $this->createEmployeeCodeVO();
        $this->createWorkingAgeVO();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\BaseIndividual::specify()
     */
    public function specify()
    {
        $this->setIndividualType(IndividualType::EMPLOYEE);
    }
}