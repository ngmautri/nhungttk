<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseCollection extends GenericCollection
{

    public function isExits(BaseChart $otherElement)
    {
        $found = $this->exists(function ($key, $element) use ($otherElement) {
            return $otherElement->getCoaCode() == $element->getCoaCode();
        });
        return $found == false;
    }
}
