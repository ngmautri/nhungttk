<?php
namespace Inventory\Application\Service\Oppening;

use Inventory\Application\Service\OpeningBalance\OpeningBalanceServiceImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpeningBalanceServiceImplFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new OpeningBalanceServiceImpl();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);
        return $service;
    }
}