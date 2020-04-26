<?php
namespace Application\Domain\EventBus\Queue\Worker;

use Application\Domain\EventBus\Event\NullEvent;
use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimpleProcurerWorker implements ProducerWorkerInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\Worker\ProducerWorkerInterface::send()
     */
    public function send($events, EventBusMiddleWareInterface $worker)
    {
        foreach ($events as $event) {
            try {
                if (false === $event instanceof NullEvent) {
                    $worker($event);
                } else {
                    break;
                }
            } catch (Exception $e) {
                // left blank
            }
        }
    }
}