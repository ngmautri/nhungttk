<?php
namespace Application\Infrastructure\Persistence;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractDoctrineRepository
{

    protected $doctrineEM;

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
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     */
    public function setDoctrineEM(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
