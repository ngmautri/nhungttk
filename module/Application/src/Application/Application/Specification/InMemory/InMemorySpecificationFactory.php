<?php
namespace Application\Application\Specification\InMemory;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class InMemorySpecificationFactory extends AbstractSpecificationFactory
{

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
        return new CurrencyExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::CanPostOnDateSpecification()
     */
    public function getCanPostOnDateSpecification()
    {
        return new CanPostOnDateSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getCompanyExitsSpecification()
     */
    public function getCompanyExitsSpecification()
    {
        return new CompanyExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseExitsSpecification()
     */
    public function getWarehouseExitsSpecification()
    {
        return new WarehouseExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getItemExitsSpecification()
     */
    public function getItemExitsSpecification()
    {
        return new ItemExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getCostCenterExitsSpecification()
     */
    public function getCostCenterExitsSpecification()
    {
        return new CostCenterExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getUserExitsSpecification()
     */
    public function getUserExitsSpecification()
    {
        return new UserExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getMeasureUnitExitsSpecification()
     */
    public function getMeasureUnitExitsSpecification()
    {
        return new MeasureUnitExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsParentSpecification()
     */
    public function getIsParentSpecification()
    {
        $spec = new IsParentSpecification();
        return $spec;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseACLSpecification()
     */
    public function getWarehouseACLSpecification()
    {
        $spec = new WarehouseACLSpecification();
        return $spec;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsFixedAssetSpecification()
     */
    public function getIsFixedAssetSpecification()
    {
        return new IsFixedAssetSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIsInventorySpecification()
     */
    public function getIsInventorySpecification()
    {
        return new IsInventoryItemSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseCodeExitsSpecification()
     */
    public function getWarehouseCodeExitsSpecification()
    {
        return new WarehouseCodeExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getWarehouseLocationExitsSpecification()
     */
    public function getWarehouseLocationExitsSpecification()
    {
        return new WarehouseLocationExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getVendorExitsSpecification()
     */
    public function getVendorExitsSpecification()
    {
        return new VendorExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getIncotermSpecification()
     */
    public function getIncotermSpecification()
    {
        return new IncotermSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getPaymentTermSpecification()
     */
    public function getPaymentTermSpecification()
    {
        return new PaymentTermSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getGLAccountExitsSpecification()
     */
    public function getGLAccountExitsSpecification()
    {
        return new GLAccountExitsSpecification();
    }

    public function getDepartmentSpecification()
    {
        return new DepartmentExitsSpecification();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecificationFactory::getAssociationItemExitsSpecification()
     */
    public function getAssociationItemExitsSpecification()
    {
        return new AssociationItemExitsSpecification();
    }

    public function getAssociationExitsSpecification()
    {
        return new AssociationExitsSpecification();
    }

    public function getNoneNegativeNumberSpecification()
    {
        return new NoneNegativeNumberSpecification();
    }
}