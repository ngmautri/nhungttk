<?php
namespace Application\Service;

use Application\Domain\Shared\AggregateRoot;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Exception;

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

    protected $logger;

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
     * @deprecated
     * @param AggregateRoot $rootEntity
     */
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
}
