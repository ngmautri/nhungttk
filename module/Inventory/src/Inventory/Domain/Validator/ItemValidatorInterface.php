<?php
namespace Inventory\Domain\Validator;

use Inventory\Domain\Item\AbstractItem;
use Procure\Domain\AbstractDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ItemValidatorInterface
{
    /**
     * 
     * @param AbstractDoc $rootEntity
     */
    public function validate(AbstractItem $rootEntity);
}

