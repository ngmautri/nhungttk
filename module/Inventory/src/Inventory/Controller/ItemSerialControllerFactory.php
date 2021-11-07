<?php
namespace Inventory\Controller;

use Inventory\Application\Reporting\ItemSerial\ItemSerialReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new ItemSerialController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('Inventory\Service\ItemSearchService');
        $controller->setItemSearchService($sv);

        $sv = $container->get(ItemSerialReporter::class);
        $controller->setItemSerialReporter($sv);

        return $controller;
    }
}