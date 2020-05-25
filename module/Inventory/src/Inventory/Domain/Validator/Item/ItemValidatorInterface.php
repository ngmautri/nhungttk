<?php
namespace Inventory\Domain\Validator\Item;

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

