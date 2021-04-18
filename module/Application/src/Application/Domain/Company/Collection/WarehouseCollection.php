<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\AccountChart\BaseChart;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseCollection extends ArrayCollection
{

    public function isExits(BaseChart $otherElement)
    {
        $found = $this->exists(function ($key, $element) use ($otherElement) {
            return $otherElement->getCoaCode() == $element->getCoaCode();
        });
        return $found == false;
    }
}
