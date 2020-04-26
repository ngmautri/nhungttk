<?php
namespace Application\Domain\EventBus\Handler\Resolver;

use Psr\Container\ContainerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PsrContainerResolver extends InteropContainerResolver
{

    /** @var ContainerInterface */
    protected $container;

    /**
     * InteropContainerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }
}