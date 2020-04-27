<?php
namespace Application\Application\Command;

use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDoctrineCmd implements CommandInterface
{

    protected $doctrineEM;

    protected $notification;

    protected $status;

    protected $estimatedDuration;

    protected $id;

    protected $options;

    protected $handler;

    protected $dto;

    protected $eventBus;

    /**
     *
     * @param EntityManager $doctrineEM
     * @param CommandOptions $inputData
     * @param CommandHandlerInterface $handler
     * @throws \Exception
     */
    public function __construct(EntityManager $doctrineEM, AbstractDTO $dto, CommandOptions $options, CommandHandlerInterface $cmdHandler, EventBusServiceInterface $eventBus = null)
    {
        if ($doctrineEM == null) {
            throw new \Exception("Entity Manager not given!");
        }
        $this->doctrineEM = $doctrineEM;
        $this->dto = $dto;
        $this->handler = $cmdHandler;
        $this->options = $options;
        $this->eventBus = $eventBus;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (! $this->handler instanceof AbstractDoctrineCmdHandler) {
            throw new \Exception(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())));
        }

        $this->handler->run($this);
    }

    /**
     *
     * @return mixed
     */
    public function getDto()
    {
        return $this->dto;
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
        return $this->notification;
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
    protected function setDoctrineEM($doctrineEM)
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
}
