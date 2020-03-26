<?php
namespace Inventory\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\SparePartsSearchService;

/*
 * @author nmt
 *
 */
class SparePartsSearchServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sparePartTable = $serviceLocator->get('Inventory\Model\MLASparepartTable');
        $eventManager = $serviceLocator->get('EventManager');

        $searchService = new SparePartsSearchService();
        $searchService->setEventManager($eventManager);
        $searchService->setSparepartTable($sparePartTable);

        return $searchService;
    }
}