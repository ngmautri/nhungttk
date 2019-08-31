<?php
namespace Inventory\Domain\Item\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemQueryRepositoryInterface
{
    public function findAll();
    public function getById($id);
    public function getByUUID($uuid);
    public function generateSysNumber($obj);    
}
