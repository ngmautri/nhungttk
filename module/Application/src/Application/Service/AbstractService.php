<?php
namespace Application\Service;

use Application\Domain\Shared\AggregateRoot;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * All Service shall extend this.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractService implements EventManagerAwareInterface
{

    protected $doctrineEM;

    protected $controllerPlugin;

    protected $eventManager;

    protected $jeService;

    protected $cache;

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

    protected function triggerEvent(AggregateRoot $rootEntity)
    {
        if (! $rootEntity instanceof AggregateRoot) {
            return;
        }

        // Triger Event
        if (count($rootEntity->getRecordedEvents() > 0)) {

            $dispatcher = new $rootEntity();

            foreach ($rootEntity->getRecordedEvents() as $event) {

                $subcribers = $rootEntity::createEventHandler(get_class($event));

                if (count($subcribers) > 0) {
                    foreach ($subcribers as $subcriber) {
                        $dispatcher->addSubscriber($subcriber);
                    }
                }
                $dispatcher->dispatch(get_class($event), $event);
            }
        }
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
     * {@inheritdoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     *
     * @return \Finance\Service\JEService
     */
    public function getJeService()
    {
        return $this->jeService;
    }

    /**
     *
     * @param \Finance\Service\JEService $jeService
     */
    public function setJeService(\Finance\Service\JEService $jeService)
    {
        $this->jeService = $jeService;
    }

    /**
     *
     * @return \Application\Controller\Plugin\NmtPlugin
     */
    public function getControllerPlugin()
    {
        return $this->controllerPlugin;
    }

    /**
     *
     * @param \Application\Controller\Plugin\NmtPlugin $controllerPlugin
     */
    public function setControllerPlugin(\Application\Controller\Plugin\NmtPlugin $controllerPlugin)
    {
        $this->controllerPlugin = $controllerPlugin;
    }
}
