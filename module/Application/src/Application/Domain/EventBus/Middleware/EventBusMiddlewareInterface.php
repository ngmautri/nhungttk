<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface EventBusMiddleWareInterface
{

    public function __invoke(EventInterface $event, callable $next = null);
}