<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;
use Psr\Log\LoggerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoggerProducerEventBusMiddleware implements EventBusMiddlewareInterface
{

    protected $logger;

    protected $queueClass;

    public function __construct(LoggerInterface $logger, $queueClass)
    {
        $this->logger = $logger;
        $this->queueClass = $queueClass;
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
        $this->logger->info(sprintf('Pushing %s to %s.', get_class($event), $this->queueClass));
    }

    /**
     *
     * @param EventInterface $event
     */
    protected function postEventLog(EventInterface $event)
    {
        $this->logger->info(sprintf('%s was pushed to %s successfully.', get_class($event), $this->queueClass));
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