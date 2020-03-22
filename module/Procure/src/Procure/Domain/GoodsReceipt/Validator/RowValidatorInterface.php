<?php
namespace Procure\Domain\PurchaseOrder\Validator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowValidatorInterface
{
    public function validate($rootEntity, $localEntity);
}

