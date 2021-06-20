<?php
namespace Inventory\Infrastructure\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LoggerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger = new Logger("InventoryLog");

        $path = './data/inventory/log';
        $filename = \sprintf("inventory_log_%s_%s_W%s.log", date('Y'), date('n'), date('W'));

        if (false === file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $handler = new StreamHandler(\sprintf("%s/%s", $path, $filename), Logger::DEBUG);
        $logger->pushHandler($handler);
        return $logger;
    }
}