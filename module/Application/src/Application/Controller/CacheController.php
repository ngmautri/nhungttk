<?php
namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Zend\Mvc\Controller\AbstractActionController;

// use Zend\Cache\Storage\Adapter\Filesystem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CacheController extends AbstractActionController
{

    protected $doctrineEM;

    protected $cacheService;

    protected $cache;

    /**
     * * @return \Symfony\Component\Cache\Adapter\AbstractAdapter
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     *
     * @param AbstractAdapter $cache
     */
    public function setCache(AbstractAdapter $cache)
    {
        $this->cache = $cache;
    }

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
        $caps = $this->cacheService->getCapabilities();

        /** @var \Zend\Cache\Storage\Adapter\Filesystem $cache_adapter ;*/
        $cache_adapter = $caps->getAdapter();
        // $cache_adapter->flush();

        // $list = $cache_adapter->getIterator();
        /*
         * foreach($list as $l){
         * //echo $l;
         * }
         */
    }

    public function clearAction()
    {
        $this->getCache()->clear();

        $this->flashMessenger()->addMessage("Cache was cleared!");

        return $this->redirect()->toUrl("/");
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

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
     *
     * @param mixed $cacheService
     */
    public function setCacheService(\Zend\Cache\Storage\StorageInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }
}
