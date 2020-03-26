<?php
namespace Inventory\Domain\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseId
{

    /**
     *
     * @var string
     */
    private $id;

    /**
     *
     * @var string
     */
    private $uuid;

    /**
     *
     * @param string $id
     * @param string $uuid
     */
    public function __construct($uuid, $id = null)
    {
        $this->id = $id;
        $this->uuid = $uuid;
    }
}
