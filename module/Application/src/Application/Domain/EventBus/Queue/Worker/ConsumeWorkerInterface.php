<?php
namespace Application\Domain\EventBus\Queue\Worker;

use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;
use Application\Domain\EventBus\Queue\QueueInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ConsumeWorkerInterface
{

    public function consume(QueueInterface $consumerQueue, QueueInterface $errorQueue, EventBusMiddleWareInterface $worker);
}