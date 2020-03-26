<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseLocationCreatedEvent extends Event
{

    const EVENT_NAME = "warehouse.location.created";

    protected $locationId;

    public function __construct()
    {}
}