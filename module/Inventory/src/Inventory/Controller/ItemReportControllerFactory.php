<?php
namespace Inventory\Controller;

use Inventory\Application\Reporting\Item\ItemReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $controller = new ItemReportController();

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $sm->get("AppCache");
        $controller->setCache($sv);

        $sv = $sm->get(ItemReporter::class);
        $controller->setReporter($sv);

        return $controller;
    }
}