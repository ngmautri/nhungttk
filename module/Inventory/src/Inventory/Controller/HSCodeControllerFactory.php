<?php
namespace Inventory\Controller;

use Inventory\Application\Service\HSCode\HSCodeTreeService;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class HSCodeControllerFactory implements FactoryInterface
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

        $controller = new HSCodeController();

        $sv = $container->get(HSCodeTreeService::class);
        $controller->setHsCodeTreeService($sv);

        $sv = $container->get(HSCodeSearchQueryImpl::class);
        $controller->setHsCodeQueryService($sv);

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('AppLogger');
        $controller->setLogger($sv);

        $sv = $container->get("AppCache");
        $controller->setCache($sv);

        return $controller;
    }
}