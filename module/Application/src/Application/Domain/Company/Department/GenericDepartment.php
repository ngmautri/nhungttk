<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\Department\Validator\Contracts\DepartmentValidatorCollection;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDepartment extends BaseDepartment
{

    public static function createFromSnapshot(GenericCompany $rootDoc, DepartmentSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, GenericCompany::class, "GenericCompanyis required!");
        Assert::isInstanceOf($snapshot, DepartmentSnapshot::class, "BaseDepartmentSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $snapshot->departmentCode = $snapshot->getDepartmentName();
        return $instance;
    }

    /**
     *
     * @param GenericCompany $rootDoc
     * @param DepartmentSnapshot $snapshot
     * @param DepartmentValidatorCollection $validators
     * @param boolean $isPosting
     * @return \Application\Domain\Company\Department\GenericDepartment
     */
    public static function validateAndCreateFromSnapshot(GenericCompany $rootDoc, DepartmentSnapshot $snapshot, DepartmentValidatorCollection $validators, $isPosting = false)
    {
        Assert::isInstanceOf($rootDoc, GenericCompany::class, "GenericCompanyis required!");
        Assert::isInstanceOf($snapshot, DepartmentSnapshot::class, "BaseDepartmentSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $snapshot->departmentCode = $snapshot->getDepartmentName();
        $instance->validate($validators, true);

        return $instance;
    }
}
