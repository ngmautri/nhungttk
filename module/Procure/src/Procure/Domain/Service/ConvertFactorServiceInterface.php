<?php
namespace Procure\Domain\Service;

/**
 * Converstion Factor
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ConvertFactorServiceInterface
{
    public function checkAndReturnFX($sourceCurrencyId, $targetCurrencyId, $fxRate);
}
