<?php
namespace HR\Domain\Employee;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseApplicant extends BaseIndividual
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\BaseIndividual::createVO()
     */
    protected function createVO()
    {
        $this->createPersonNameVO();
        $this->createWorkingAgeVO();
    }
}