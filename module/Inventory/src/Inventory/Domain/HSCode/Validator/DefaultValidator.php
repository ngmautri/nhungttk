<?php
namespace Inventory\Domain\HSCode\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Contracts\MonitorMethod;
use Inventory\Domain\Validator\Item\AbstractItemValidator;
use Inventory\Domain\Validator\Item\ItemValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultValidator extends AbstractItemValidator implements ItemValidatorInterface
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

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getCompanyUserExSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError("User is not identified for this transaction. #" . $rootEntity->getCreatedBy());
            }

            if ($rootEntity->getLastchangeBy() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastchangeBy()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("User is not identified for this transaction. #" . $rootEntity->getLastchangeBy());
                }
            }

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();
            if ($spec->isSatisfiedBy($rootEntity->getItemName())) {
                $rootEntity->addError("Item name is null or empty. It is required for any item.");
            } else {

                if (preg_match('/[$^]/', $rootEntity->getItemName()) == 1) {
                    $err = "Item name contains invalid character (e.g. $)";
                    $rootEntity->addError($err);
                }
            }

            // Monitor method is not for service item.
            if ($rootEntity->getMonitoredBy() !== null && $rootEntity->getItemTypeId() !== ItemType::SERVICE_ITEM_TYPE) {
                if (! \in_array($rootEntity->getMonitoredBy(), MonitorMethod::getSupportedMethod())) {
                    $format = "Monitor method not supported! #%s";
                    $rootEntity->addError(\sprintf($format, $rootEntity->getMonitoredBy()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

