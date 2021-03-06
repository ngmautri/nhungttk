<?php
namespace Procure\Application\Specification\Zend;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Shared\Specification\AbstractSpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ProcureSpecificationFactory extends AbstractSpecificationFactory
{

    /**
     *
     * @var EntityManager
     */
    protected $doctrineEM;

    /*
     * @param EntityManager $doctrineEM
     */
    public function __construct(EntityManager $doctrineEM)
    {
        if (! $doctrineEM instanceof EntityManager) {
            throw new InvalidArgumentException(sprintf("Entity Doctrine manager not found! %s", __METHOD__));
        }
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Shared\Specification\AbstractSpecificationFactory::getPoRowSpecification()
     */
    public function getPoRowSpecification()
    {
        return new PoRowSpecification($this->doctrineEM);
    }

    public function getPrRowSpecification()
    {
        return new PrRowSpecification($this->doctrineEM);
    }
}