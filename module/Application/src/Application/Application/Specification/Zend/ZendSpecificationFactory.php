<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Doctrine\ORM\EntityManager;
use User\Infrastructure\Persistence\DoctrineUserRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ZendSpecificationFactory extends AbstractSpecificationFactory
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
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getEmailSpecification()
     */
    public function getEmailSpecification()
    {
        return new EmailSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getDateSpecification()
     */
    public function getDateSpecification()
    {
        return new DateSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getNullorBlankSpecification()
     */
    public function getNullorBlankSpecification()
    {
        return new NullorBlankSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getPositiveNumberSpecification()
     */
    public function getPositiveNumberSpecification()
    {
        return new PositiveNumberSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getCurrencyExitsSpecification()
     */
    public function getCurrencyExitsSpecification()
    {
        return new CurrencyExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::CanPostOnDateSpecification()
     */
    public function getCanPostOnDateSpecification()
    {
        return new CanPostOnDateSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getCompanyExitsSpecification()
     */
    public function getCompanyExitsSpecification()
    {
        return new CompanyExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseExitsSpecification()
     */
    public function getWarehouseExitsSpecification()
    {
        return new WarehouseExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getItemExitsSpecification()
     */
    public function getItemExitsSpecification()
    {
        return new ItemExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getCostCenterExitsSpecification()
     */
    public function getCostCenterExitsSpecification()
    {
        return new CostCenterExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getUserExitsSpecification()
     */
    public function getUserExitsSpecification()
    {
        return new UserExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getMeasureUnitExitsSpecification()
     */
    public function getMeasureUnitExitsSpecification()
    {
        return new MeasureUnitExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsParentSpecification()
     */
    public function getIsParentSpecification()
    {
        $spec = new IsParentSpecification($this->doctrineEM);

        $userRep = new DoctrineUserRepository();
        $userRep->setDoctrineEM($this->doctrineEM);
        $spec->setUserRepository($userRep);
        return $spec;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseACLSpecification()
     */
    public function getWarehouseACLSpecification()
    {
        $spec = new WarehouseACLSpecification($this->doctrineEM);

        $userRep = new DoctrineUserRepository();
        $userRep->setDoctrineEM($this->doctrineEM);
        $spec->setUserRepository($userRep);
        return $spec;
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
    public function setDoctrineEM($doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
    
    
   

   
   
  
    

   
   

  
}