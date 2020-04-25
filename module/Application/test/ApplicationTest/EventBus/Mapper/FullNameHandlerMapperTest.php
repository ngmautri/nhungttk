<?php
namespace ApplicationTest\EventBus\Mapper;

use ApplicationTest\EventBus\DummyEvent;
use ApplicationTest\EventBus\DummyEvent2Handler;
use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use PHPUnit_Framework_TestCase;

class FullNameHandlerMapperTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testItCanResolveHandler()
    {
        $strategy = new FullNameHandlerMapper([
            DummyEventHandler::class,
            DummyEvent2Handler::class
        ]);

        $event = new DummyEvent();
        $handlers = $strategy->handlerName($event);

        // $this->assertContains(DummyEvent::class . 'Handler', reset($handlers));

        var_dump($handlers);
    }

    public function testItWillThrowExceptionIfClassDoesNotExist()
    {
        $this->expectException(\RuntimeException::class);
        new FullNameHandlerMapper([
            'RandomHandler'
        ]);
    }

    public function testItWillThrowExceptionIfClassDoesNotImplementEventHandler()
    {
        $this->expectException(\RuntimeException::class);
        new FullNameHandlerMapper([
            'DateTime'
        ]);
    }

    public function testItWillThrowExceptionIfEventNotSupported()
    {
        $strategy = new FullNameHandlerMapper([]);
        $event = new DummyEvent();

        // $this->expectException(\RuntimeException::class);
        $strategy->handlerName($event);
    }
}