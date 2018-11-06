<?php
namespace Procure\Model\Qo;

use Doctrine\ORM\EntityManager;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class DownloadStrategyAbstract
{
    protected $doctrineEM;
    
    abstract public function doDownloadRows($qo, $rows);
    abstract public function doDownload($qo);
    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
    
    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}