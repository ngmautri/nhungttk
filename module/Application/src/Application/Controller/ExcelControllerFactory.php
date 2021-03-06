<?php
namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 *
 * @deprecated
 * @author nmt
 *        
 */
class ExcelControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new ExcelController();

        // User Table
        $tbl = $container->get('User\Model\UserTable');
        $controller->setUserTable($tbl);

        // User Table
        /*
         * $tbl = $sm->get ('User\Model\AclResourceTable' );
         * $controller->setAclResourceTable($tbl);
         */
        // Auth Service
        $sv = $container->get('AuthService');
        $controller->setAuthService($sv);

        // Auth Service
        $sv = $container->get('Application\Service\ExcelService');
        $controller->setExcelService($sv);

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        return $controller;
    }
}