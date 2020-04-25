<?php
namespace Application\Domain\EventBus;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBus implements EventBusMiddleWareInterface
{

    protected $middleware;

    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $eventBusMiddleware) {
            Assert::isInstanceOf($eventBusMiddleware, EventBusMiddleWareInterface::class);
        }

        $this->middleware = $middleware;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface::__invoke()
     */
    public function __invoke(EventInterface $event, callable $next = null)
    {
        $middleware = $this->middleware;
        $current = array_shift($middleware);

        if (empty($middleware) && ! empty($current)) {
            $current->__invoke($event);

            return;
        }

        foreach ($middleware as $eventBusMiddleware) {
            $callable = function ($event) use ($eventBusMiddleware) {
                return $eventBusMiddleware($event);
            };

            $current->__invoke($event, $callable);
            $current = $eventBusMiddleware;
        }
    }
}