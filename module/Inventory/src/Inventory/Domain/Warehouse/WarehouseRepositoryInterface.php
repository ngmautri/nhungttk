<?php
namespace Inventory\Domain\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface WarehouseRepositoryInterface
{

    public function findAll();

    public function getById($id);

    public function getByUUID($uuid);

    public function store(Warehouse $wh);

    public function getLocationById();

    public function getLocationOf();
}
