<?php
namespace Inventory\Application\Specification\Doctrine;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineSpecificationFactory extends \Inventory\Domain\AbstractSpecificationFactory
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\AbstractSpecificationFactory::getOnhandQuantitySpecification()
     */
    public function getOnhandQuantitySpecification()
    {
        return new OnhandQuantitySpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\AbstractSpecificationFactory::getTranactionExitsSpecification()
     */
    public function getTranactionExitsSpecification()
    {
        return new TransactionExitsSpecification($this->doctrineEM);
    }

    public function getWarehouseExitsSpecification()
    {
        return new WarehouseExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\AbstractSpecificationFactory::getItemExitsSpecification()
     */
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
        if ($doctrineEM == null)
            throw new InvalidArgumentException("Doctrine Entity Manager is required!");

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