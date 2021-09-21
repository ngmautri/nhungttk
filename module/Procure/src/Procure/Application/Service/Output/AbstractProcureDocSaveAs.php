<?php
namespace Procure\Application\Service\Output;

use Psr\Log\LoggerInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractProcureDocSaveAs
{

    protected $logger;

    protected $filter;

    public function __toString()
    {
        return get_class($this->getFilter());
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
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

    protected function logException(Exception $e)
    {
        if ($this->getLogger() != null) {
            $this->getLogger()->alert($e->getMessage());
        }
    }

    /**
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     *
     * @param mixed $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
}
