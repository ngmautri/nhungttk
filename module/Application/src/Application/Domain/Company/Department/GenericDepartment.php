<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Company\GenericCompany;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDepartment extends BaseDepartment
{

    public static function createFromSnapshot(GenericCompany $rootDoc, BaseDepartmentSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, GenericCompany::class, "GenericCompanyis required!");
        Assert::isInstanceOf($snapshot, BaseDepartmentSnapshot::class, "BaseDepartmentSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}
