<?php
namespace Application\Application\Command\Doctrine;

use Application\Notification;
use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Stopwatch\Stopwatch;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractCommand implements CommandInterface
{

    protected $doctrineEM;

    protected $notification;

    protected $status;

    protected $estimatedDuration;

    protected $id;

    protected $options;

    /**
     *
     * @var AbstractCommandHandler ;
     */
    protected $handler;

    protected $data;

    protected $eventBus;

    protected $logger;

    protected $cache;

    protected $output;

    protected $companyVO;

    /**
     *
     * @param EntityManager $doctrineEM
     * @param array $data
     * @param CommandOptions $options
     * @param CommandHandlerInterface $cmdHandler
     * @param EventBusServiceInterface $eventBus
     */
    public function __construct(EntityManager $doctrineEM, $data, CommandOptions $options, CommandHandlerInterface $cmdHandler, EventBusServiceInterface $eventBus = null)
    {
        Assert::isInstanceOf($doctrineEM, EntityManager::class, 'Entity Manager not given!');
        Assert::isInstanceOf($options, CommandOptions::class, 'Command option  not given!');
        Assert::isInstanceOf($cmdHandler, AbstractCommandHandler::class, 'Command handler not given!');

        $this->doctrineEM = $doctrineEM;
        $this->data = $data;
        $this->handler = $cmdHandler;
        $this->options = $options;
        $this->eventBus = $eventBus;
    }

    private function clearNotification()
    {
        $this->notification = null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        $stopWatch = new Stopwatch();
        $stopWatch->start("cmd_run");
        $this->clearNotification();
        $this->handler->run($this);
        $timer = $stopWatch->stop("cmd_run");
        $this->setEstimatedDuration($timer->getDuration());
    }

    /**
     *
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    public function load()
    {}

    public function save()
    {}

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
     * @return mixed
     */
    public function getNotification()
    {
        if ($this->notification != null) {
            return $this->notification;
        }

        return new Notification();
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @return mixed
     */
    public function getEstimatedDuration()
    {
        return $this->estimatedDuration;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     */
    protected function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @param mixed $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     *
     * @param mixed $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @param mixed $estimatedDuration
     */
    protected function setEstimatedDuration($estimatedDuration)
    {
        $this->estimatedDuration = $estimatedDuration;
    }

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return \Application\Domain\Shared\Command\CommandOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @return mixed
     */
    public function getEventBus()
    {
        return $this->eventBus;
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
     * @return mixed
     */
    public function getOutput()
    {
        return $this->getHandler()->getOutput();
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function addError($err)
    {
        if ($err == null) {
            return $this;
        }

        $notification = $this->getNotification();
        $notification->addError($err);
        $this->notification = $notification;
        return $this;
    }

    public function addWaring($mes)
    {
        if ($mes == null) {
            return $this;
        }

        $notification = $this->getNotification();
        $this->notification = $notification->addWarning($mes);
        return $this;
    }

    public function addSuccess($mes)
    {
        if ($mes == null) {
            return $this;
        }

        $notification = $this->getNotification();
        $this->notification = $notification->addSuccess($mes);
        return $this;
    }

    public function logException(\Exception $e)
    {
        if ($this->logger != null) {
            $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
        }
    }

    public function logInfo($message)
    {
        if ($this->logger != null) {
            $this->logger->info($message);
        }
    }

    public function logAlert($message)
    {
        if ($this->logger != null) {
            $this->logger->alert($message);
        }
    }
}
