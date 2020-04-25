<?php
namespace Application\Domain\EventBus\Worker;

use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;
use Application\Domain\EventBus\Queue\QueueInterface;
use Exception;
use NullEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimplerWorker implements WorkerInterface
{

    public function consume(QueueInterface $consumerQueue, QueueInterface $errorQueue, EventBusMiddleWareInterface $worker)
    {
        while ($event = $consumerQueue->pop()) {
            try {
                if (false === $event instanceof \NullEvent) {
                    $worker($event);
                } else {
                    break;
                }
            } catch (Exception $e) {
                if (! empty($event) && (false === $event instanceof NullEvent)) {
                    $errorQueue->push($event);
                }
            }
        }
    }
}