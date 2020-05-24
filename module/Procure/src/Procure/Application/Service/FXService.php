<?php
namespace Procure\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Infrastructure\AggregateRepository\DoctrinePostingPeriodQueyrRepository;
use Application\Service\AbstractService;
use Procure\Domain\Service\Contracts\FXServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FXService extends AbstractService implements FXServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\FXServiceInterface::checkAndReturnFX()
     */
    public function checkAndReturnFX($sourceCurrencyId, $targetCurrencyId, $fxRate)
    {
        $specFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $spec = $specFactory->getCurrencyExitsSpecification();
        if (! $spec->isSatisfiedBy($sourceCurrencyId) || ! $spec->isSatisfiedBy($targetCurrencyId)) {
            throw new \Exception("Currency not exits");
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
