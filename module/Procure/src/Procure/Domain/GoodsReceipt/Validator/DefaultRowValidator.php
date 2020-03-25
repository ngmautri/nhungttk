<?php
namespace Procure\Domain\GoodsReceipt\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\Exception\GrCreateException;
use Procure\Domain\Exception\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;

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
     * @see \Procure\Domain\PurchaseOrder\Validator\RowValidatorInterface::validate()
     */
    public function validate($rootEntity, $localEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new GrInvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof GRRow) {
            throw new GrInvalidArgumentException('GR Row not given!');
        }

        // do verification now

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

            // ======= UNIT PRICE ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
                $localEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
            }

            // ======= CONVERSION FACTORY ==========
            if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
                $localEntity->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
            }
            // ======= EXW PRICE ==========
            if (! $spec->isSatisfiedBy($localEntity->getExwUnitPrice())) {
                // $notification->addError("Exw Unit price is not valid! " . $localEntity->getExwUnitPrice());
            }

            if (! $localEntity->getTaxRate() == null) {
            if (! $spec->isSatisfiedBy($localEntity->getTaxRate())) {
                $localEntity->addError("Tax Rate is not valid! " . $localEntity->getTaxRate());
            }
        }
        
        
        } catch (GrCreateException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

