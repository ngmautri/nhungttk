<?php
namespace Application\Domain\Util\Collection\Export;

use Application\Domain\Util\Collection\Contracts\ExportInterface;
use Psr\Log\LoggerInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractExport implements ExportInterface
{

    protected $logger;

    protected $filter;

    protected $formatter;

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

    /**
     *
     * @return mixed
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     *
     * @param mixed $formatter
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }
}
