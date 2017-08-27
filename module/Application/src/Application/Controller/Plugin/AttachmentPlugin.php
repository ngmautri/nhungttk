<?php
namespace Application\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * 
 * @author nmt
 *
 */
class AttachmentPlugin extends AbstractPlugin
{

    protected $serviceManager;
    protected $doctrineEM;

    public function getAttachmentSerive()
    {
        return $this->getServiceManager()->get("Application\Service\AttachmentService");
    }

    /**
     *
     * @return the $serviceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     *
     * @param field_type $serviceManager
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
