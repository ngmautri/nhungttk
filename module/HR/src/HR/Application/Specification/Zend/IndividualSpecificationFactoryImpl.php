<?php
namespace HR\Application\Specification\Zend;

use Doctrine\ORM\EntityManager;
use HR\Application\Specification\Zend\Employee\EmployeeCodeExitsSpecification;
use HR\Domain\Validator\AbstractIndividualSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndividualSpecificationFactoryImpl extends AbstractIndividualSpecificationFactory
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

    /*
     * |=============================
     * |Implementation
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Validator\AbstractIndividualSpecificationFactory::getEmployeeCodeExitsSpecification()
     */
    public function getEmployeeCodeExitsSpecification()
    {
        return new EmployeeCodeExitsSpecification($this->getDoctrineEM());
    }
}