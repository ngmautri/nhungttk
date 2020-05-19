<?php
namespace Application\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSpecificationFactory
{

    abstract function getDateSpecification();

    abstract function getEmailSpecification();

    abstract function getNullorBlankSpecification();

    abstract function getPositiveNumberSpecification();

    abstract function getCurrencyExitsSpecification();

    abstract function getCanPostOnDateSpecification();

    abstract function getCompanyExitsSpecification();

    abstract function getWarehouseExitsSpecification();

    abstract function getWarehouseCodeExitsSpecification();

    abstract function getWarehouseLocationExitsSpecification();

    abstract function getItemExitsSpecification();

    abstract function getUserExitsSpecification();

    abstract function getCostCenterExitsSpecification();

    abstract function getMeasureUnitExitsSpecification();

    abstract function getIsParentSpecification();

    abstract function getWarehouseACLSpecification();

    abstract function getIsFixedAssetSpecification();

    abstract function getIsInventorySpecification();

    abstract function getVendorExitsSpecification();

    abstract function getIncotermSpecification();

    abstract function getPaymentTermSpecification();

    abstract function getDepartmentSpecification();

    abstract function getGLAccountExitsSpecification();
}
