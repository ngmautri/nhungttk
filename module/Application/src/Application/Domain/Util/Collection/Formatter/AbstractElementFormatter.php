<?php
namespace Application\Domain\Util\Collection\Formatter;

use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Psr\Log\LoggerInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractElementFormatter implements ElementFormatterInterface
{

    protected $cache;

    protected $logger;

    protected $locale;

    /*
     * |=============================
     * |Method
     * |
     * |=============================
     */
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

    /*
     * |=============================
     * |Setter and Getter
     * |
     * |=============================
     */

    /**
     *
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
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
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param mixed $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}

