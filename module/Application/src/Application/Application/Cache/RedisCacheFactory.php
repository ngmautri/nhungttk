<?php
namespace Application\Application\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RedisCacheFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $client = RedisAdapter::createConnection('redis://192.168.116.129:6379', [
            'compression' => true,
            'lazy' => false,
            'persistent' => 0,
            'persistent_id' => null,
            'tcp_keepalive' => 0,
            'timeout' => 30,
            'read_timeout' => 0,
            'retry_interval' => 0
        ]);
        $cache = new RedisAdapter($client, 'mla-app');
        return $cache;
    }
}