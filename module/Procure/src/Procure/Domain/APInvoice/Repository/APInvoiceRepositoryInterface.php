<?php
namespace Procure\Domain\APInvoice\Repository;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
Interface APInvoiceRepositoryInterface
{
    public function getById($id);
    
    public function getByUUID($uuid);
    
    
}
