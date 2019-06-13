<?php
namespace Application\Infrastructure\Persistence;


use Application\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractDoctrineRepository
{

    protected $doctrineEM;
    
    public function __construct(\Doctrine\ORM\EntityManager $doctrineEM){
        if($doctrineEM ==null){
            throw new InvalidArgumentException();
        }
        $this->doctrineEM = $doctrineEM;
    }
    

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
