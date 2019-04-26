<?php
namespace BP\Model\Domain\Vendor;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Interface InterfaceVendorRepository
{

    public function getById($id);

    public function store(Vendor $aggregate);
}
