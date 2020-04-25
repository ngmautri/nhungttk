<?php
namespace Application\Domain\EventBus\Worker;

use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;
use Application\Domain\EventBus\Queue\QueueInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface WorkerInterface
{

    public function consume(QueueInterface $consumerQueue, QueueInterface $errorQueue, EventBusMiddleWareInterface $worker);
}