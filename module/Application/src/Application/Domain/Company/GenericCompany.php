<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericCompany extends AbstractCompany
{

    protected $warehouses;

    protected $departments;

    protected $postingPeriods;

    public function createValueObject()
    {
        $vo = new CompanyVO();
        GenericObjectAssembler::updateAllFieldsFrom($vo, $this);
        return $vo;
    }

    public function constructFromDB(CompanySnapshot $snapshot)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($this, $snapshot);
    }
}
