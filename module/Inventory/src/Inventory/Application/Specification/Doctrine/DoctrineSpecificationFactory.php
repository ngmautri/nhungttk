<?php
namespace Inventory\Application\Specification\Doctrine;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineSpecificationFactory extends \Inventory\Domain\AbstractSpecificationFactory
{

    protected $doctrineEM;

    public function getWarehouseExitsSpecification()
    {
        return new WarehouseExitsSpecification($this->doctrineEM);
    }

    public function getItemExitsSpecification()
    {
        return new ItemExitsSpecification($this->doctrineEM);
    }

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