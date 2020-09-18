<?php
namespace Inventory\Application\Specification\Inventory;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Validator\AbstractInventorySpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventorySpecificationFactoryImpl extends AbstractInventorySpecificationFactory
{

    protected $doctrineEM;

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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\AbstractInventorySpecificationFactory::getOnhandQuantitySpecification()
     */
    public function getOnhandQuantitySpecification()
    {
        return new OnhandQuantitySpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\AbstractInventorySpecificationFactory::getOnhandQuantityAtLocationSpecification()
     */
    public function getOnhandQuantityAtLocationSpecification()
    {
        return new OnhandQuantityAtLocationSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\AbstractInventorySpecificationFactory::getOnhandQuantityOfMovementSpecification()
     */
    public function getOnhandQuantityOfMovementSpecification()
    {
        return new OnhandQuantityOfMovementSpecification($this->doctrineEM);
    }
}