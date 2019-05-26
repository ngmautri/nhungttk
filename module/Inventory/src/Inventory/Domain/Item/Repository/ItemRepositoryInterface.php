<?php
namespace Inventory\Domain\Item\Repository;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemRepositoryInterface
{
    public function findAll();
    public function getById($id);
    public function getByUUID($uuid);
    public function store(GenericItem $itemAggregate, $generateSysNumber = True);
    public function generateSysNumber($obj);    
}
