<?php
namespace HR\Domain\Validator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractHrSpecificationFactory
{

    abstract function getEmployeeCodeExitsSpecification();
}
