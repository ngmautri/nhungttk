<?php
namespace Inventory\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\AssetSearchService;

/*
 * @author nmt
 *
 */
class AssetSearchServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        // get RegisterListener
        $assetTable = $serviceLocator->get('Inventory\Model\MLAAssetTable');
        $eventManager = $serviceLocator->get('EventManager');

        $searchService = new AssetSearchService();
        $searchService->setEventManager($eventManager);
        $searchService->setAssetTable($assetTable);

        return $searchService;
    }
}