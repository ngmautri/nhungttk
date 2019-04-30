<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrinePurchaseRequestRepository implements InterfacePurchaseRequestRepository
{
    public function getAll()
    {}

    public function getById($id)
    {}

    public function get($id)
    {}

    public function getByUuid($uuid)
    {}

    public function store(InterfacePurchaseRequest $aggregate)
    {}
    
}
