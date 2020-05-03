<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Inventory\Domain\Transaction\AbstractTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface HeaderValidatorInterface
{

    /**
     *
     * @param AbstractTrx $rootEntity
     */
    public function validate(AbstractTrx $rootEntity);
}

