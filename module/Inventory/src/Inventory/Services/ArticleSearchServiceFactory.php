<?php
namespace Inventory\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\ArticleSearchService;

/*
 * @author nmt
 *
 */
class ArticleSearchServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tbl = $serviceLocator->get('Inventory\Model\ArticleTable');
        $eventManager = $serviceLocator->get('EventManager');

        $searchService = new ArticleSearchService();
        $searchService->setEventManager($eventManager);
        $searchService->setArticleTable($tbl);

        return $searchService;
    }
}