<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Handler\Mapper\EventHandlerMapperInterface;
use Application\Domain\EventBus\Handler\Resolver\EventHandlerResolverInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBusMiddleware implements EventBusMiddlewareInterface
{

    protected $handlerMapper;

    protected $handlerResolver;

    public function __construct(EventHandlerMapperInterface $handlerMapper, EventHandlerResolverInterface $handlerResolver)
    {
        $this->handlerMapper = $handlerMapper;
        $this->handlerResolver = $handlerResolver;
    }

    public function __invoke(EventInterface $event, callable $next = null)
    {
        $handlerNames = $this->handlerMapper->handlerName($event);

        foreach ($handlerNames as $handlerLists) {
            foreach ($handlerLists as $handlerName) {
                $handlerInstance = $this->handlerResolver->instantiate($handlerName);
                $handlerInstance($event);
            }
        }

        if ($next) {
            $next($event);
        }
    }
}