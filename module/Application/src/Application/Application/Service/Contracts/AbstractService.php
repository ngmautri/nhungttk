<?php
namespace Application\Application\Service\Contracts;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Exception;

/**
 * All Service shall extend this.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractService
{

    protected $doctrineEM;

    protected $cache;

    protected $logger;

    protected function logInfo($message)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->info($message);
        }
    }

    protected function logAlert($message)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->alert($message);
        }
    }

    protected function logException(Exception $e, $trace = true)
    {
        if ($this->getLogger() == null) {
            return;
        }

        if ($trace) {
            $this->getLogger()->alert($e->getTraceAsString());
        } else {
            $this->getLogger()->alert($e->getMessage());
        }
    }

    /**
     *
     * @return \Symfony\Component\Cache\Adapter\AbstractAdapter
     */
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
     * @param EntityManager $doctrineEM
     * @return \Procure\Service\GrService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
