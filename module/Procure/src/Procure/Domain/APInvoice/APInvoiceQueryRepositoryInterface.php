<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APInvoiceQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($trxId, $token = null);

    public function getByUUID($uuid);
}
