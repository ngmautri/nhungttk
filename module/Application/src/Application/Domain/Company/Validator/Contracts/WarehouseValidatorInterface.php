<?php
namespace Application\Domain\Warehouse\Validator\Contracts;

use Inventory\Domain\Warehouse\BaseWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface CompanyValidatorInterface
{

    public function validate(BaseWarehouse $rootEntity);
}

