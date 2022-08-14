<?php
namespace Procure\Domain\PurchaseRequest\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRRow;
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
        if (! $rootEntity instanceof GenericPR) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof PRRow) {
            throw new InvalidArgumentException('GR Row not given!');
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
            // ======= QUANTITY ==========

            // ======= CONVERSION FACTORY ==========
            if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
                $localEntity->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
            }
            // ======= CONVERSION FACTORY ==========

            // ======= EDT Date ==========

            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($localEntity->getEdt())) {
                $localEntity->addError("EDT Date is not correct or empty");
                $localEntity->addError(\sprintf("EDT Date is not correct or empty - %s", $localEntity->getEdt()));
            } else {
                $edt = new \DateTime($localEntity->getEdt());
                $today = new \DateTime();

                if ($edt < $today) {
                    // $localEntity->addError(\sprintf("EDT date is in the past. Today is %s", \date_format($today, "Y-m-d")));
                    $localEntity->addError("EDT date is in the past");
                } elseif ($edt < $today->modify("1 days")) {
                    $localEntity->addError(\sprintf("EDT date is invalid! It required at least 01 days for buying!"));
                }
            }
            // ======= EDT Date ==========
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

