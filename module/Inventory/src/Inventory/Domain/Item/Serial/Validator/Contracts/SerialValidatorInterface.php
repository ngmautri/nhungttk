<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Serial\BaseSerial;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface SerialValidatorInterface
{

    public function validate(BaseSerial $rootEntity);
}

