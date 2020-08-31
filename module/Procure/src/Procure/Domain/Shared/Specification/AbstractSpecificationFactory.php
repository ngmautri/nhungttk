<?php
namespace Procure\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSpecificationFactory
{

    abstract function getPoRowSpecification();

    abstract function getPrRowSpecification();
}

