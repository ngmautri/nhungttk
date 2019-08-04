<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseLocationUpdatedEvent extends Event
{

    const EVENT_NAME = "warehouse.location.updated";

    protected $locationId;

    public function __construct()
    {}
}