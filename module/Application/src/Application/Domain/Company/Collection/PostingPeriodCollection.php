<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\PostingPeriod\BasePostingPeriod;
use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostingPeriodCollection extends GenericCollection
{

    public function isExits(BasePostingPeriod $otherElement)
    {
        $found = $this->exists(function ($key, $element) use ($otherElement) {
            return $otherElement->getCoaCode() == $element->getCoaCode();
        });
        return $found == false;
    }
}
