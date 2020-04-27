<?php
namespace Application\Domain\EventBus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface EventBusServiceInterface
{

    public function dispatch($events);
}