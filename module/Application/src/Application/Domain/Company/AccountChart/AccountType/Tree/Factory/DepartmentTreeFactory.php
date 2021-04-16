<?php
namespace Application\Application\Service\Department\Tree\Factory;

use Application\Application\Service\Department\Tree\DepartmentTree;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DepartmentTreeFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new DepartmentTree();
        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        return $service;
    }
}