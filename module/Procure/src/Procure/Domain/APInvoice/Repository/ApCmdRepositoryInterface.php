<?php
namespace Procure\Domain\APInvoice\Repository;

use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ApCmdRepositoryInterface
{

    /**
     * Store whole Document
     *
     * @param GenericGR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function store(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericGR $rootEntity
     * @param boolean $generateSysNumber
     */
    public function post(GenericGR $rootEntity, $generateSysNumber = True);

    /**
     *
     * @param GenericGR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function storeHeader(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericGR $rootEntity
     * @param GRRow $localEntity
     * @param boolean $isPosting
     */
    public function storeRow(GenericGR $rootEntity, GRRow $localEntity, $isPosting = false);
}
