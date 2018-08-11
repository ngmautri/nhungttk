<?php
namespace Procure\Service;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * All Procure Service shall extend this.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractProcureService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $controllerPlugin;

    protected $eventManager;

    protected $jeService;

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
     * {@inheritdoc}
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__
        ));
        $this->eventManager = $eventManager;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * 
     *  @return \Finance\Service\JEService
     */
    public function getJeService()
    {
        return $this->jeService;
    }

    /**
     *
     * @param mixed $jeService
     */
    public function setJeService(\Finance\Service\JEService $jeService)
    {
        $this->jeService = $jeService;
    }
    
   /**
    * 
    *  @return \Application\Controller\Plugin\NmtPlugin
    */
    public function getControllerPlugin()
    {
        return $this->controllerPlugin;
    }

    /**
     * 
     *  @param \Application\Controller\Plugin\NmtPlugin $controllerPlugin
     */
    public function setControllerPlugin(\Application\Controller\Plugin\NmtPlugin $controllerPlugin)
    {
        $this->controllerPlugin = $controllerPlugin;
    }

}
