<?php
namespace HR\Domain\Validator\Employee;

use HR\Domain\Employee\BaseIndividual;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface IndividualValidatorInterface
{

    /**
     *
     * @param BaseIndividual $rootEntity
     */
    public function validate(BaseIndividual $rootEntity);
}

