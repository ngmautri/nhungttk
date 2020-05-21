<?php
namespace Procure\Infrastructure\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CacheFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $path = './data/cache/symfony/';
        $cachePool = new FilesystemAdapter('mla-app-procure', 100, $path);
        return $cachePool;
    }
}