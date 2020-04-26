<?php
namespace Application\Application\Service\Eventbus;

use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use Application\Domain\EventBus\Middleware\LoggerEventBusMiddleware;
use Application\Service\AbstractService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBusService extends AbstractService
{

    protected $handlers;

    protected $mapper;

    protected $resolver;

    /**
     *
     * @param array $events
     */
    public function dispatch($events)
    {
        \Webmozart\Assert\Assert::isArray($events);

        $logger = new Logger("EventBus");
        $handler = new StreamHandler('./data/log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $middleware = [
            new LoggerEventBusMiddleware($logger),
            new EventBusMiddleware($this->mapper, $this->resolver)
        ];

        $eventBus = new EventBus($middleware);

        foreach ($events as $event) {
            $eventBus->__invoke($event, $middleware);
        }
    }
}
