<?php
namespace Application\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Service\CompanyServiceInterface;
use Application\Infrastructure\AggregateRepository\DoctrinePostingPeriodQueyrRepository;
use Application\Service\AbstractService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyServiceService extends AbstractService implements CompanyServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Service\CompanyServiceInterface::getLocalCurrency()
     */
    public function getLocalCurrency($companyId)
    {
        $localCurrencyId = null;

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $company ;
         */
        $company = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtApplicationCompany')
            ->find($companyId);
        if ($company !== null) {
            return $company->getId(0);
        }

        return $localCurrencyId;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Service\CompanyServiceInterface::checkAndReturnFX()
     */
    public function checkAndReturnFX($companyId, $sourceCurrencyId, $targetCurrencyId, $fxRate)
    {
        $specFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $spec = $specFactory->getCurrencyExitsSpecification();
        if (! $spec->isSatisfiedBy($sourceCurrencyId) || ! $spec->isSatisfiedBy($targetCurrencyId)) {
            throw new \Exception("Currency not exits!" . __FUNCTION__);
        }

        if ($sourceCurrencyId == $targetCurrencyId) {
            return 1;
        }

        $spec = $specFactory->getPositiveNumberSpecification();
        $postingPeriodRep = new DoctrinePostingPeriodQueyrRepository($this->getDoctrineEM());
        $systemRate = $postingPeriodRep->getLatestFX($sourceCurrencyId, $targetCurrencyId);

        if (! $spec->isSatisfiedBy($fxRate) and $systemRate == null) {
            throw new \Exception(sprintf("Exchange rate not valid (%s/%s)", $fxRate, $systemRate));
        }

        if (! $spec->isSatisfiedBy($fxRate) and $systemRate !== null) {
            return $systemRate;
        }

        if ($spec->isSatisfiedBy($fxRate) and $systemRate !== null) {
            if ($fxRate / $systemRate >= 1.1 || $systemRate / $fxRate >= 1.1) {
                throw new \Exception(sprintf("Exchange rate is different than 10pct (%s/%s)", $fxRate, $systemRate));
            }
        }

        return $fxRate;
    }
}
