<?php
namespace HR\Application\Specification\Zend;

use Doctrine\ORM\EntityManager;
use HR\Domain\Validator\AbstractHrSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use HR\Application\Specification\Zend\Employee\EmployeeCodeExitsSpecification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class HRSpecificationFactoryImpl extends AbstractHrSpecificationFactory
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
     * @see \HR\Domain\Validator\AbstractHrSpecificationFactory::getEmployeeCodeExitsSpecification()
     */
    public function getEmployeeCodeExitsSpecification()
    {
        return new EmployeeCodeExitsSpecification($this->getDoctrineEM());
    }
}