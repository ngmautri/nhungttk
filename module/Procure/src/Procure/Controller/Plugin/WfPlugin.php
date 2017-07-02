<?php
namespace Procure\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * 
 * @author nmt
 *
 */
class WfPlugin extends AbstractPlugin
{
    protected $serviceManager;
    protected $doctrineEM;
    
    public function getWF() {
        echo "test plugin";
    }
    /**
     * @return the $serviceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param field_type $serviceManager
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
