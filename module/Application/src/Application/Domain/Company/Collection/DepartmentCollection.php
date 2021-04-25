<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\Department\BaseDepartment;
use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentCollection extends GenericCollection
{

    public function isUnique(BaseDepartment $deparment)
    {
        $found = $this->exists(function ($key, $element) use ($deparment) {
            return $deparment->getDepartmentName() == $element->getDepartmentName();
        });
        return $found == false;
    }
}
