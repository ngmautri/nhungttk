<?php
namespace BP\Domain\Vendor\Repository;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Interface VendorRepositoryInterface
{

    public function getById($id);

    public function getByUUID($uuid);
}
