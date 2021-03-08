<?php
namespace HR\Domain\Employee\Repository;

use HR\Domain\Contracts\Repository\CmdRepositoryInterface;
use HR\Domain\Employee\BaseIndividual;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface IndividualCmdRepositoryInterface extends CmdRepositoryInterface
{

    /**
     *
     * @param BaseIndividual $rootEntity
     * @param boolean $generateSysNumber
     */
    public function store(BaseIndividual $rootEntity, $generateSysNumber = True);
}
