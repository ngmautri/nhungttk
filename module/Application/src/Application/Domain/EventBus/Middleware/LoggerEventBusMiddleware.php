<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;
use Psr\Log\LoggerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoggerEventBusMiddleware implements EventBusMiddlewareInterface
{

    protected $logger;

    /**
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface::__invoke()
     */
    public function __invoke(EventInterface $event, callable $next = null)
    {
        try {
            if ($next) {
                $this->preEventLog($event);
                $next($event);
                $this->postEventLog($event);
            }
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     *
     * @param EventInterface $event
     */
    protected function preEventLog(EventInterface $event)
    {
        $this->logger->info(sprintf('Starting %s handling.', get_class($event)));
    }

    /**
     *
     * @param EventInterface $event
     */
    protected function postEventLog(EventInterface $event)
    {
        $this->logger->info(sprintf('%s was handled successfully.', get_class($event)));
    }

    /**
     *
     * @param \Exception $e
     */
    protected function logException(\Exception $e)
    {
        $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
    }
}