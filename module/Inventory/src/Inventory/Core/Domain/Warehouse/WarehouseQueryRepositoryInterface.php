<?php
namespace Inventory\Domain\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface WarehouseQueryRepositoryInterface
{

    public function findAll();

    public function getById($id);

    public function getByUUID($uuid);

    public function getLocationById();

    public function getLocationOf();
}
