<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\Department\BaseDepartment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentCollection extends ArrayCollection
{

    public function isUnique(BaseDepartment $deparment)
    {
        $found = $this->exists(function ($key, $element) use ($deparment) {
            return $deparment->getDepartmentName() == $element->getDepartmentName();
        });
        return $found == false;
    }
}
