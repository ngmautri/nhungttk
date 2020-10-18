<?php
namespace Application\Domain\Shared\ValueObject;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class ValueObject
{

    protected $specFactory;

    function __construct($value, AbstractSpecificationFactory $specFactory = null)
    {}

    // abstract public function equals(AbstractValueObject $obj);
    abstract public function __toString();
}
