<?php
namespace HR\Domain\Employee;

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
    protected function createVO()
    {
        $this->createPersonNameVO();
        $this->createEmployeeCodeVO();
        $this->createWorkingAgeVO();
    }
}