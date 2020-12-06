<?php
namespace Application\Application\EventBus\Contracts;

use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractEventHandler implements EventHandlerInterface, EventHandlerPriorityInterface
{

    protected $doctrineEM;

    protected $eventBusService;

    protected $logger;

    protected $cache;

    protected $companyVO;

    protected $userVO;

    /**
     *
     * @param EntityManager $doctrineEM
     * @param EventBusServiceInterface $eventBusService
     */
    public function __construct(EntityManager $doctrineEM, EventBusServiceInterface $eventBusService)
    {
        $this->doctrineEM = $doctrineEM;
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return \Application\Domain\EventBus\EventBusServiceInterface
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }

    public function getCache()
    {
        return $this->cache;
    }

    /**
     *
     * @param AbstractAdapter $cache
     */
    public function setCache(AbstractAdapter $cache)
    {
        $this->cache = $cache;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function logException(\Exception $e)
    {
        if ($this->logger == null) {
            return;
        }
        $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
    }

    protected function logInfo($m)
    {
        if ($this->logger != null) {
            $this->logger->info($m);
        }
    }

    /**
     *
     * @param int $companyId
     * @return \Application\Domain\Company\CompanyVO
     */
    public function getCompanyVO($companyId)
    {
        $rep1 = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        return $rep1->getById($companyId)->createValueObject();
    }

    /**
     *
     * @return mixed
     */
    public function getUserVO()
    {
        return $this->userVO;
    }
}