<?php
namespace ApplicationTest\EventBus;

use Psr\Log\AbstractLogger;

class InMemoryLogger extends AbstractLogger
{

    /** @var array */
    protected $logs = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->logs[$level][] = $message;
    }

    /**
     *
     * @return array
     */
    public function logs()
    {
        return $this->logs;
    }
}