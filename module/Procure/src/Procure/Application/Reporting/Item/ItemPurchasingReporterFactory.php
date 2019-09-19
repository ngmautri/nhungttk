<?php
namespace Procure\Application\Reporting\Item;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Application\Reporting\Item\ItemPurchasingReporter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemPurchasingReporterFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new ItemPurchasingReporter();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('Procure\Infrastructure\Persistence\Doctrine\PRListRepository');
        $service->setPrListRespository($sv);

        return $service;
    }
}