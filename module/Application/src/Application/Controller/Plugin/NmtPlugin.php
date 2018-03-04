<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
class NmtPlugin extends AbstractPlugin
{

    protected $doctrineEM;

    /**
     *
     * @var \Doctrine\ORM\EntityManager $doctrineEM ;
     *      $doctrineEM = $this->NmtPlugin()->doctrineEM();
     * @return unknown
     */
    public function doctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
