<?php
namespace ApplicationTest\EventBus\Middleware;

use ApplicationTest\EventBus\DummyEvent;
use ApplicationTest\EventBus\DummyEvent2Handler;
use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Domain\EventBus\Handler\Resolver\SimpleArrayResolver;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use Application\Domain\EventBus\Middleware\LoggerEventBusMiddleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PHPUnit_Framework_TestCase;

class EventBusTest extends PHPUnit_Framework_TestCase
{

    protected $handlers;

    protected $mapper;

    protected $resolver;

    public function setUp()
    {
        $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },

            DummyEvent2Handler::class => function () {
                return new DummyEvent2Handler();
            }
        ];

        $this->resolver = new SimpleArrayResolver($handlers);

        $this->mapper = new FullNameHandlerMapper([
            DummyEventHandler::class,
            DummyEvent2Handler::class
        ]);

        $this->resolver = new SimpleArrayResolver($handlers);
    }

    public function testItCanStackMiddleware()
    {
        // $logger = new InMemoryLogger();
        $logger = new Logger("EventBus");

        $path = __DIR__ . '/log/';
        if (false === file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $handler = new StreamHandler($path . '/test.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $middleware = [
            new LoggerEventBusMiddleware($logger),
            new EventBusMiddleware($this->mapper, $this->resolver)
        ];

        $eventBus = new EventBus($middleware);
        $eventBus->__invoke(new DummyEvent());
        $this->assertNotEmpty($logger);
    }
}