<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Exception\InvalidArgumentException;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Doctrine\ORM\EntityManager;

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
        if (! $doctrineEM instanceof EntityManager) {
            throw new InvalidArgumentException(sprintf("Entity Doctrine manager not found! %s", __METHOD__));
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
    public function setDoctrineEM($doctrineEM)
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
        return $spec;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsFixedAssetSpecification()
     */
    public function getIsFixedAssetSpecification()
    {
        return new IsFixedAssetSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsInventorySpecification()
     */
    public function getIsInventorySpecification()
    {
        return new IsInventoryItemSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseCodeExitsSpecification()
     */
    public function getWarehouseCodeExitsSpecification()
    {
        return new WarehouseCodeExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseLocationExitsSpecification()
     */
    public function getWarehouseLocationExitsSpecification()
    {
        return new WarehouseLocationExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getVendorExitsSpecification()
     */
    public function getVendorExitsSpecification()
    {
        return new VendorExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIncotermSpecification()
     */
    public function getIncotermSpecification()
    {
        return new IncotermSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getPaymentTermSpecification()
     */
    public function getPaymentTermSpecification()
    {
        return new PaymentTermSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getGLAccountExitsSpecification()
     */
    public function getGLAccountExitsSpecification()
    {
        return new GLAccountExitsSpecification($this->doctrineEM);
    }

    public function getDepartmentSpecification()
    {
        return new DepartmentExitsSpecification($this->doctrineEM);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getAssociationItemExitsSpecification()
     */
    public function getAssociationItemExitsSpecification()
    {
        return new AssociationItemExitsSpecification($this->doctrineEM);
    }

    public function getAssociationExitsSpecification()
    {
        return new AssociationExitsSpecification($this->doctrineEM);
    }

    public function getNoneNegativeNumberSpecification()
    {
        return new NoneNegativeNumberSpecification($this->doctrineEM);
    }

    public function getCompanyUserExSpecification()
    {}
}