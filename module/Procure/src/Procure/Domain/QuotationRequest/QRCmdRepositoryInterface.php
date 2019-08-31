<?php
namespace Procure\Domain\QuotationRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface QRCmdRepositoryInterface
{

    public function store(AbstractQR $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(AbstractQR $rootEntity, $generateSysNumber = True);

    public function storeHeader(AbstractQR $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(AbstractQR $rootEntity, QRRow $row, $isPosting = false);

    public function createRow($id, AbstractQR $row, $isPosting = false);
}
