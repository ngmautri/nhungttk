<?php
namespace Procure\Infrastructure\Persistence\Doctrine\Factory;

use Procure\Infrastructure\Persistence\DoctrineQOListRepository;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrineQOListRepositoryFactory implements FactoryInterface
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
        
        $controller = new QOListRepository();

        $sv = $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		return $controller;
	}	
	
}