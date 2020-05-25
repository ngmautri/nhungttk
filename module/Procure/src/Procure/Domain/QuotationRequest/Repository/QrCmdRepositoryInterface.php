<?php
namespace Procure\Domain\QuotationRequest\Repository;

use Procure\Domain\Contracts\Repository\CmdRepositoryInterface;
use Procure\Domain\QuotationRequest\GenericQR;
use Procure\Domain\QuotationRequest\QRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface QrCmdRepositoryInterface extends CmdRepositoryInterface
{

    /**
     *
     * @param GenericQR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function store(GenericQR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericQR $rootEntity
     * @param boolean $generateSysNumber
     */
    public function post(GenericQR $rootEntity, $generateSysNumber = True);

    /**
     *
     * @param GenericQR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     */
    public function storeHeader(GenericQR $rootEntity, $generateSysNumber = false, $isPosting = false);

    /**
     *
     * @param GenericQR $rootEntity
     * @param QRRow $localEntity
     * @param boolean $isPosting
     */
    public function storeRow(GenericQR $rootEntity, QRRow $localEntity, $isPosting = false);
}
