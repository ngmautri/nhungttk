<?php
namespace Procure\Application\Specification\Zend;

use Doctrine\ORM\EntityManager;
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
        $this->doctrineEM = $doctrineEM;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\Shared\Specification\AbstractSpecificationFactory::getPoRowSpecification()
     */
    public function getPoRowSpecification()
    {
        return new PoRowSpecification($this->doctrineEM);
    }


    
}