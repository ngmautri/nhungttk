<?php
namespace Inventory\Domain\Item\Component\Validator\Contracts;

use Inventory\Domain\Item\CompositeItem;
use Inventory\Domain\Item\Component\BaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ComponentValidatorInterface
{

    public function validate(CompositeItem $rootEntity, BaseComponent $localEntity);
}

