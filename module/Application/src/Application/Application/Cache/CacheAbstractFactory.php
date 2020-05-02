<?php
namespace Application\Application\Logger;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CacheAbstractFactory implements AbstractFactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // $path = "C:\NMT\nmt-workspace\mla-2.6.8\data\cache";
        $path = __DIR__ . "./cache";
        $cachePool = new FilesystemAdapter('app', 10, $path);
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');
        return isset($config['DB']['adapters'][$requestedName]) ? true : false;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');
        return (object) $config['DB']['adapters'][$requestedName];
    }
}