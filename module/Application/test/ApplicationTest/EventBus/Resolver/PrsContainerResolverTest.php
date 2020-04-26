<?php
namespace ApplicationTest\EventBus\Resolver;

use ApplicationTest\Bootstrap;
use Application\Domain\EventBus\Handler\Resolver\PsrContainerResolver;
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPosted;
use PHPUnit_Framework_TestCase;

class PrsContainerResolverTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \Zend\ServiceManager\ServiceManager $container ;
     */
    protected $container;

    public function setUp()
    {
        $this->container = Bootstrap::getServiceManager();
    }

    public function testItCanResolve()
    {
        $resolver = $this->container->get('Procure\Application\Eventbus\HandlerResolver');
        $instance = $resolver->getResolver()->instantiate(UpdateIndexOnApPosted::class);
        \var_dump($instance);
        $this->assertInstanceOf(UpdateIndexOnApPosted::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(\InvalidArgumentException::class);
        $resolver = new PsrContainerResolver($this->container);
        $resolver->instantiate('\Application\Application\Event\Handler\DummyEventHandler');
    }
}