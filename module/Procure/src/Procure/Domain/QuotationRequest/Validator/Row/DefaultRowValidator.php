<?php
namespace Procure\Domain\QuotationRequest\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\QuotationRequest\GenericQR;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericQR) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof QRRow) {
            throw new InvalidArgumentException('Quotation Row not given!');
        }

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ======= ITEM ==========
            $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "itemId" => $localEntity->getItem()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Item #%s not exits in the company #%s", $localEntity->getItem(), $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            // ======= QUANTITY ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
                $localEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
            }

            // ======= CONVERSION FACTORY ==========
            if (! $spec->isSatisfiedBy($localEntity->getStandardConvertFactor())) {
                $localEntity->addError("Standard convert factor is not valid! " . $localEntity->getStandardConvertFactor());
            }

            if ($localEntity->getDiscountRate() !== null) {
                if (! $spec->isSatisfiedBy($localEntity->getDiscountRate())) {
                    $format = 'Discount rate is not valid! %s%s';
                    $m = \sprintf($format, $localEntity->getDiscountRate(), '%');
                    $localEntity->addError($m);
                } else {
                    if ($localEntity->getDiscountRate() > 100) {
                        $format = 'Discount rate is not valid! Too high: %s%s';
                        $m = \sprintf($format, $localEntity->getDiscountRate(), '%');
                        $localEntity->addError($m);
                    }
                }
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

