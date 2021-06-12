<?php
namespace Inventory\Domain\Item\Batch\Validator;

use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Item\Batch\BaseBatch;
use Inventory\Domain\Item\Serial\BaseSerial;
use Inventory\Domain\Item\Variant\Validator\Contracts\BatchValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BatchDefaultValidator extends AbstractValidator implements BatchValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\BatchValidatorInterface::validate()
     */
    public function validate(BaseBatch $rootEntity)
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