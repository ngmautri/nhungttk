<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowValidatorInterface
{

    /**
     *
     * @param AbstractTrx $rootEntity
     * @param BaseRow $localEntity
     */
    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity);
}

