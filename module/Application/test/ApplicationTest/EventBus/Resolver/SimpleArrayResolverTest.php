<?php
namespace ApplicationTest\EventBus\Resolver;

use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\Handler\Resolver\SimpleArrayResolver;
use PHPUnit_Framework_TestCase;

class SimpleArrayResolverTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testItCanResolve()
    {
        $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            }
        ];

        $resolver = new SimpleArrayResolver($handlers);
        $instance = $resolver->instantiate(DummyEventHandler::class);
        $this->assertInstanceOf(DummyEventHandler::class, $instance);
        var_dump($instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $resolver = new SimpleArrayResolver([]);
        $resolver->instantiate('Hello\World');
    }
}