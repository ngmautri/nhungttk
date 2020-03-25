<?php
namespace Procure\Domain\GoodsReceipt\Validator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowValidatorInterface
{
    public function validate($rootEntity, $localEntity);
}

