<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\SparepartsController;

/*
 * @author nmt
 *
 */
class SparepartsControllerFactory implements FactoryInterface
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

        $controller = new SparepartsController();

        // User Table
        $tbl = $sm->get('User\Model\UserTable');
        $controller->setUserTable($tbl);

        // Spare Part table
        $tbl = $sm->get('Inventory\Model\MLASparepartTable');
        $controller->setSparePartTable($tbl);

        $tbl = $sm->get('Inventory\Model\SparepartPictureTable');
        $controller->setSparePartPictureTable($tbl);

        $tbl = $sm->get('Inventory\Model\SparepartMovementsTable');
        $controller->setSparepartMovementsTable($tbl);

        $tbl = $sm->get('Inventory\Model\SparepartCategoryTable');
        $controller->setSparePartCategoryTable($tbl);

        $tbl = $sm->get('Inventory\Model\SparepartCategoryMemberTable');
        $controller->setSparePartCategoryMemberTable($tbl);

        // Purchase Request Cart Item table
        $tbl = $sm->get('Procurement\Model\PurchaseRequestCartItemTable');
        $controller->setPurchaseRequestCartItemTable($tbl);

        // Purchase Request Cart Item table
        $tbl = $sm->get('Procurement\Model\PurchaseRequestItemTable');
        $controller->setPrItemTable($tbl);

        // SP Minumum Balance table
        $tbl = $sm->get('Inventory\Model\SparepartMinimumBalanceTable');
        $controller->setSpMinimumBalanceTable($tbl);

        $tbl = $sm->get('Inventory\Services\SparepartService');
        $controller->setSparePartService($tbl);

        // Auth Service
        $sv = $sm->get('AuthService');
        $controller->setAuthService($sv);

        return $controller;
    }
}