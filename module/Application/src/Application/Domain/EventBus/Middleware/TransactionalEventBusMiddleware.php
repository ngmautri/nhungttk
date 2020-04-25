<?php
namespace Application\Domain\EventBus\Middleware;

use Application\Domain\EventBus\Event\EventInterface;
use PDO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionalEventBusMiddleware implements EventBusMiddlewareInterface
{

    /** @var PDO */
    protected $pdo;

    /**
     * TransactionalEventBusMiddleware constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Middleware\EventBusMiddleWareInterface::__invoke()
     */
    public function __invoke(EventInterface $event, callable $next = null)
    {
        try {
            $this->pdo->beginTransaction();
            $next($event);
            $this->pdo->commit();
        } catch (\PDOException $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
