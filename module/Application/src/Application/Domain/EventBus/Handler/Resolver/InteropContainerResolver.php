<?php
namespace Application\Domain\EventBus\Handler\Resolver;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InteropContainerResolver implements EventHandlerResolverInterface
{

    /** @var ContainerInterface */
    protected $container;

    /**
     * InteropContainerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function instantiate(string $handler)
    {
        if (false === $this->container->has($handler)) {
            throw new InvalidArgumentException(sprintf('Handler %s could not be found. Did you register it?', $handler));
        }

        return $this->container->get($handler);
    }
}