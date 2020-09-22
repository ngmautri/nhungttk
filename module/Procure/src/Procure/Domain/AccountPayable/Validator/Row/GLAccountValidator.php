<?php
namespace Procure\Domain\AccountPayable\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GLAccountValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericAP) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof APRow) {
            throw new InvalidArgumentException('GR Row not given!');
        }
        // do verification now

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ======= GL Account ==========
            $spec = $this->sharedSpecificationFactory->getGLAccountExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "glAccountId" => $localEntity->getGlAccount()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("GL #%s not exits in the company #%s", $localEntity->getGlAccount(), $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();

            // ======= COST CENTER =========
            if ($localEntity->getCostCenter() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "costCenter" => $localEntity->getCostCenter()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $localEntity->addError(sprintf("Cost center #%s not exits in company #%s", $localEntity->getCostCenter(), $rootEntity->getCompany()));
                }
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

