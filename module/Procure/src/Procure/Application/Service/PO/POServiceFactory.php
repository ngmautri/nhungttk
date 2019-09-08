<?php
namespace Procure\Application\Service\PO;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new POService();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $queryRepository = $container->get('Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository');
        $service->setQueryRepository($queryRepository);

        $cmdRepository = $container->get('Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository');
        $service->setCmdRepository($cmdRepository);
        
        return $service;
    }
}