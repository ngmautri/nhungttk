<?php
namespace Application\Controller\Plugin;

use Application\Application\Service\Shared\CommonCollection;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SharedCollectionPluginFactory implements FactoryInterface
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

        $collection = $container->get(CommonCollection::class);
        $p = new SharedCollectionPlugin($collection);
        return $p;
    }
}