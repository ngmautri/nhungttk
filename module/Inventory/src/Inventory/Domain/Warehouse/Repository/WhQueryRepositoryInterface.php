<?php
namespace Inventory\Domain\Warehouse\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface WhQueryRepositoryInterface
{

    public function getById($id);

    public function getByTokenId($id, $token);

    public function getByUUID($uuid);

    public function getLocationById();

    public function getLocations($warehouseId);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersion($id, $token = null);

    /**
     *
     * @param int $id
     * @param string $token
     */
    public function getVersionArray($id, $token = null);
}
