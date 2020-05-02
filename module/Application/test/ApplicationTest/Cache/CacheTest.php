<?php
namespace ApplicationTest\EventBus\Middleware;

use ApplicationTest\Bootstrap;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use PHPUnit_Framework_TestCase;

class CacheTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testItCanStackMiddleware()
    {
        /**
         *
         * @var AbstractAdapter $cache
         */
        $cache = Bootstrap::getServiceManager()->get('AppCache');
        var_dump($cache->clear());
    }
}