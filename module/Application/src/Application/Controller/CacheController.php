<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\Cache\Storage\StorageInterface;
//use Zend\Cache\Storage\Adapter\Filesystem;


/**
 *
 * @author nmt
 *        
 */
class CacheController extends AbstractActionController
{

    protected $doctrineEM;
    protected $cacheService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     */
    public function cacheSpaceAction()
    {
        /** @var \Zend\Cache\Storage\Capabilities $caps ;*/
        $caps =  $this->cacheService->getCapabilities();
        
        /** @var \Zend\Cache\Storage\Adapter\Filesystem $cache_adapter ;*/        
        $cache_adapter =  $caps->getAdapter();
        //$cache_adapter->flush();
        
       // $list = $cache_adapter->getIterator();
        /* foreach($list as $l){
            //echo $l;
        } */
     
     }

    /**
     *
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @return mixed
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * @param mixed $cacheService
     */
    public function setCacheService(StorageInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

}
