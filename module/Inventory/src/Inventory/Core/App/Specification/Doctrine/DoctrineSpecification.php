<?php
namespace Inventory\Application\Specification\Doctrine;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class DoctrineSpecification extends AbstractSpecification
{

    protected $doctrineEM;

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function __construct(EntityManager $doctrineEM)
    {
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
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}