<?php
namespace Inventory\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Listener\PictureUploadListener;

/*
 * @author nmt
 *
 */
class PictureUploadListenerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listener = new PictureUploadListener();
        return $listener;
    }
}