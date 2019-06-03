<?php
namespace Procure\Infrastructure\Persistence\Factory;

use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrinePRListRepositoryFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;
        
        $controller = new DoctrinePRListRepository();

        $sv = $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		return $controller;
	}	
	
}