<?php
namespace Inventory\Domain\Item\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Validator\Item\AbstractItemValidator;
use Inventory\Domain\Validator\Item\ItemValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LogisticDataValidator extends AbstractItemValidator implements ItemValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Item\ItemValidatorInterface::validate()
     */
    public function validate(AbstractItem $rootEntity)
    {
        if (! $rootEntity instanceof AbstractItem) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            if ($rootEntity->getStandardWeightInKg() != null) {
                if (! $spec->isSatisfiedBy($rootEntity->getStandardWeightInKg())) {
                    $rootEntity->addError("Standard weight invalid!");
                }
            }

            if ($rootEntity->getStandardVolumnInM3() != null) {
                if (! $spec->isSatisfiedBy($rootEntity->getStandardVolumnInM3())) {
                    $rootEntity->addError("Standard volumn invalid!");
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

