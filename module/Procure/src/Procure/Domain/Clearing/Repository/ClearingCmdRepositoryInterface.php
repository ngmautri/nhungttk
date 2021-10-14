<?php
namespace Procure\Domain\Clearing\Repository;

use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\GenericAP;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ClearingCmdRepositoryInterface
{

    /**
     *
     * @param \Procure\Domain\AccountPayable\GenericAP $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function store(GenericAP $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param \Procure\Domain\AccountPayable\GenericAP $rootEntity
     * @param boolean $generateSysNumber
     */
    public function post(GenericAP $rootEntity, $generateSysNumber = True);

    /**
     *
     * @param \Procure\Domain\AccountPayable\GenericAP $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function storeHeader(GenericAP $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param \Procure\Domain\AccountPayable\GenericAP $rootEntity
     * @param \Procure\Domain\AccountPayable\APRow $localEntity
     * @param boolean $isPosting
     */
    public function storeRow(GenericAP $rootEntity, APRow $localEntity, $isPosting = false);

    /**
     *
     * @param GenericAP $rootEntity
     * @param APRow $localEntity
     */
    public function removeRow(GenericAP $rootEntity, APRow $localEntity);
}
