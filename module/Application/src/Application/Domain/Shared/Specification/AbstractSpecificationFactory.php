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
    abstract function getItemExitsSpecification();
    abstract function getCostCenterExitsSpecification();
}
