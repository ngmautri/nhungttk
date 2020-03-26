<?php
namespace Application\Service;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SmtpWebDeServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $transport = new SmtpTransport();

        $config = $container->get('config');
        $smtpConf = $config['smtp_WEB_DE'];
        $options = new SmtpOptions($smtpConf);

        $transport->setOptions($options);
        return $transport;
    }
}