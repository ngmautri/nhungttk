<?php
namespace Application\Domain\EventBus\Handler\Resolver;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SimpleArrayResolver implements EventHandlerResolverInterface
{

    /** @var array */
    protected $handlers = [];

    /**
     * ArrayResolver constructor.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Handler\Resolver\EventHandlerResolverInterface::instantiate()
     */
    public function instantiate($handler)
    {
        if (false === isset($this->handlers[$handler])) {
            throw new \InvalidArgumentException(sprintf('Handler %s could not be found. Did you register it?', $handler));
        }

        $callable = $this->handlers[$handler];

        return ($callable instanceof \Closure) ? $callable() : new $callable();
    }
}