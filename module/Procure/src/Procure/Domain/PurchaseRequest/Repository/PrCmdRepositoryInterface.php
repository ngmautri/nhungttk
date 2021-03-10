<?php
namespace Procure\Domain\PurchaseRequest\Repository;

use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface PrCmdRepositoryInterface
{

    /**
     *
     * @param GenericPR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function store(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericPR $rootEntity
     * @param boolean $generateSysNumber
     */
    public function post(GenericPR $rootEntity, $generateSysNumber = True);

    /**
     *
     * @param GenericPR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function storeHeader(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericPR $rootEntity
     * @param PRRow $localEntity
     * @param boolean $isPosting
     */
    public function storeRow(GenericPR $rootEntity, PRRow $localEntity, $isPosting = false);

    /**
     *
     * @param GenericPR $rootEntity
     * @param PRRow $localEntity
     */
    public function removeRow(GenericPR $rootEntity, PRRow $localEntity);
}
