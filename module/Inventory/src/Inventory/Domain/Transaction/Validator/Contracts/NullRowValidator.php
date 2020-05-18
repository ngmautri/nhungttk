<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NullRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity)
    {
        // do nothing.
    }
}

