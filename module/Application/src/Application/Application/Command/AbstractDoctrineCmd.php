<?php
namespace Application\Application\Command;

use Application\Domain\Shared\Command\CommandInterface;
use Doctrine\ORM\EntityManager;
use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\AbstractDTO;

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

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getDto()
    {
        return $this->dto;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     *
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     *
     * @param mixed $handler
     */
    protected function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param array $inputData
     * @param CommandHandlerInterface $handler
     * @throws \Exception
     */
    public function __construct(EntityManager $doctrineEM, AbstractDTO $dto, array $options, CommandHandlerInterface $handler = null)
    {
        if ($doctrineEM == null) {
            throw new \Exception("Entity Manager not given!");
        }
        $this->doctrineEM = $doctrineEM;
        $this->dto = $dto;
        $this->handler = $handler;
        $this->options = $options;
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

  
}
