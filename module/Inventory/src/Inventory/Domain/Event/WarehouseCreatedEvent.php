<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseCreatedEvent extends Event
{

    const EVENT_NAME = "inventory.warehouse.created";

    protected $warehouseId;

    public function __construct()
    {}
}