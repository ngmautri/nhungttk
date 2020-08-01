<?php
namespace Inventory\Domain\Transaction\GI\Validator\Header;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultGIHeaderValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new \InvalidArgumentException('GenericTrx entity not given!');
        }
        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

