<?php
namespace Inventory\Domain\Item\Serial\Validator;

use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Item\Serial\BaseSerial;
use Inventory\Domain\Item\Variant\Validator\Contracts\SerialValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SerialDefaultValidator extends AbstractValidator implements SerialValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\SerialValidatorInterface::validate()
     */
    public function validate(BaseSerial $rootEntity)
    {
        if (! $rootEntity instanceof BaseSerial) {
            $rootEntity->addError("BaseSerial object not found");
            return;
        }

        try {
        /**
         *
         * @todo
         */
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}