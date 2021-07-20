<?php
namespace Application\Application\EventBus;

use Application\Domain\EventBus\Handler\Resolver\PsrContainerResolver;
use Application\Service\AbstractService;
use Psr\Container\ContainerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PsrHandlerResolver extends AbstractService
{

    protected $container;

    protected $resolver;

    public function getResolver()
    {
        return new PsrContainerResolver($this->container);
    }

    /**
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @param mixed $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }
}
