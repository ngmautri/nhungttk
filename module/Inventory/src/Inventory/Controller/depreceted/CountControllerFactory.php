<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\CountController;

/*
 * @author nmt
 *
 */
class CountControllerFactory implements FactoryInterface
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

        $controller = new CountController();

        // Spare Part table
        $tbl = $sm->get('Inventory\Model\MLAAssetTable');
        $controller->setAssetTable($tbl);

        $tbl = $sm->get('Inventory\Model\AssetCategoryTable');
        $controller->setAssetCategoryTable($tbl);

        $tbl = $sm->get('Inventory\Model\AssetCountingTable');
        $controller->setAssetCountingTable($tbl);

        $tbl = $sm->get('Inventory\Model\AssetCountingItemTable');
        $controller->setAssetCountingItemTable($tbl);

        $tbl = $sm->get('Inventory\Model\AssetPictureTable');
        $controller->setAssetPictureTable($tbl);

        $sv = $sm->get('AuthService');
        $controller->setAuthService($sv);

        $sv = $sm->get('Inventory\Services\AssetSearchService');
        $controller->setAssetSearchService($sv);

        $sv = $sm->get('Inventory\Services\AssetService');
        $controller->setAssetService($sv);

        return $controller;
    }
}