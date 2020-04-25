<?php
namespace ApplicationTest\EventBus\Middleware;

use ApplicationTest\EventBus\DummyEvent;
use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Domain\EventBus\Handler\Resolver\SimpleArrayResolver;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use PHPUnit_Framework_TestCase;
use ApplicationTest\EventBus\DummyEvent2Handler;

class EventBusMiddlewareTest extends PHPUnit_Framework_TestCase
{

    protected $eventBus;

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

        $resolver = new SimpleArrayResolver($handlers);

        $mapper = new FullNameHandlerMapper([
            DummyEventHandler::class,
            DummyEvent2Handler::class
        ]);

        $this->eventBus = new EventBusMiddleware($mapper, $resolver);
    }

    public function testItCanHandle()
    {
        $this->eventBus->__invoke(new DummyEvent(), function () {
            return;
        });
        $this->assertTrue(true);
    }
}