<?php
namespace Inventory\Domain;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSpecificationFactory
{
    abstract function getWarehouseExitsSpecification();
    abstract function getItemExitsSpecification();
}
