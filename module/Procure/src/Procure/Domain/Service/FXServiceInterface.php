<?php
namespace Procure\Domain\Service;

/**
 * Exchange Rate Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FXServiceInterface
{
    public function checkAndReturnFX($sourceCurrencyId, $targetCurrencyId, $fxRate);
}
