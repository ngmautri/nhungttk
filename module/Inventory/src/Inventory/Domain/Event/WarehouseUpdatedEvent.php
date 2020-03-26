<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseUpdatedEvent extends Event
{

    const EVENT_NAME = "inventory.warehouse.updated";

    protected $warehouseId;

    public function __construct()
    {}
}