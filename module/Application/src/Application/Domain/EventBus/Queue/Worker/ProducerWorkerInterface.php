<?php
namespace Application\Domain\EventBus\Queue\Worker;

use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ProducerWorkerInterface
{

    public function send($events, EventBusMiddleWareInterface $worker);
}