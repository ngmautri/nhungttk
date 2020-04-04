<?php
namespace Application\Application\Command;

use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Shared\Command\CompositeCommand;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDoctrineCompositeCmd extends CompositeCommand
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
     *
     * @param EntityManager $doctrineEM
     * @param CommandOptions $inputData
     * @param CommandHandlerInterface $handler
     * @throws \Exception
     */
    public function __construct(EntityManager $doctrineEM)
    {
        if ($doctrineEM == null) {
            throw new \Exception("Entity Manager not given!");
        }
        $this->doctrineEM = $doctrineEM;
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
   

}
