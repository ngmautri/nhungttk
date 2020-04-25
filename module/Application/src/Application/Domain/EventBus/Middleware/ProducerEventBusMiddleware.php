<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Queue\QueueInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoggerEventBusMiddleware implements EventBusMiddlewareInterface
{

    protected $queue;

    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface::__invoke()
     */
    public function __invoke(EventInterface $event, callable $next = null)
    {
        $this->queue->push($event);

        if ($next) {
            $next($event);
        }
    }
}