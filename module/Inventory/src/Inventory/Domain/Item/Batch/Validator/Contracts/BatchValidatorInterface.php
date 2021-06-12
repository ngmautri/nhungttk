<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Batch\BaseBatch;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface BatchValidatorInterface
{

    public function validate(BaseBatch $rootEntity);
}

