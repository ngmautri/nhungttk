<?php
namespace Inventory\Model\Domain\Item;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
Interface InterfaceItemRepository
{
    public function getById($id);
    
    public function getByUuid($uuid);
    
    public function store(Item $aggregate);
    
    public function getAll();
}
