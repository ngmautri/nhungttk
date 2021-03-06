<?php
namespace Inventory\Domain\Validator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractInventorySpecificationFactory
{

    abstract function getOnhandQuantitySpecification();

    abstract function getOnhandQuantityAtLocationSpecification();

    abstract function getOnhandQuantityOfMovementSpecification();
}
