<?php
namespace Inventory\Domain\Component\Validator\Contracts;

use Inventory\Domain\Item\Component\BaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ComponentValidatorInterface
{

    public function validate(BaseComponent $rootEntity);
}

