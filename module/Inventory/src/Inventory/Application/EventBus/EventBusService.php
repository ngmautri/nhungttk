<?php
namespace Inventory\Application\Eventbus;

use Application\Application\Eventbus\PsrHandlerResolver;
use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use Application\Domain\EventBus\Middleware\LoggerEventBusMiddleware;
use Application\Domain\EventBus\Middleware\ProducerEventBusMiddleware;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Procure\Application\EventBus\Queue\DoctrineQueue;
use Procure\Application\Eventbus\HandlerMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBusService implements EventBusServiceInterface
{

    protected $mapper;

    protected $resolver;

    protected $doctrineEM;

    protected $queueName = "mla.inventory";

    protected $logger;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\EventBusServiceInterface::dispatch()
     */
    public function dispatch($events)
    {
        foreach ($events as $event) {
            $this->getEventbus()->__invoke($event);
            $this->getDoctrineEventbus()->__invoke($event);
        }
    }

    /**
     *
     * @return \Application\Domain\EventBus\EventBus
     */
    public function getEventbus()
    {
        // $logger = new InMemoryLogger();
        $middleware = [
            new LoggerEventBusMiddleware($this->logger),
            new EventBusMiddleware($this->mapper->getHandlerMapper(), $this->resolver->getResolver())
        ];

        return new EventBus($middleware);
    }

    /**
     *
     * @return \Application\Domain\EventBus\EventBus
     */
    public function getDoctrineEventbus()
    {
        $queue = new DoctrineQueue($this->getDoctrineEM(), $this->queueName);

        $middleware = [
            new LoggerEventBusMiddleware($this->logger),
            new ProducerEventBusMiddleware($queue)
        ];
        return new EventBus($middleware);
    }

    // ===========================================

    /**
     *
     * @param mixed $mapper
     */
    public function setMapper(HandlerMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     *
     * @param mixed $resolver
     */
    public function setResolver(PsrHandlerResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     *
     * @return mixed
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     *
     * @return mixed
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @param mixed $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}
    
