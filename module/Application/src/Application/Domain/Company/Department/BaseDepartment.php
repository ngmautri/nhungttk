<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Company\Department\Validator\Contracts\DepartmentValidatorCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseDepartment extends AbstractDepartment
{

    /**
     *
     * @param BaseDepartment $localEntity
     * @param DepartmentValidatorCollection $validators
     * @param boolean $isPosting
     */
    protected function validate(DepartmentValidatorCollection $validators, $isPosting = false)
    {
        if ($validators == null) {
            throw new \InvalidArgumentException("Department validators not found!");
        }
        $validators->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }
}
