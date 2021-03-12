<?php
namespace HR\Domain\Employee\LabourContract\Repository;

use HR\Domain\Contracts\Repository\CmdRepositoryInterface;
use HR\Domain\Employee\LabourContract\BaseLabourContract;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface LabourContractCmdRepositoryInterface extends CmdRepositoryInterface
{

    /**
     *
     * @param BaseLabourContract $rootEntity
     * @param boolean $generateSysNumber
     */
    public function store(BaseLabourContract $rootEntity, $generateSysNumber = True);
}
