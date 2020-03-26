<?php
namespace Application\Domain\Service;

/**
 * Company Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface CompanyServiceInterface
{

    public function checkAndReturnFX($companyId, $sourceCurrencyId, $targetCurrencyId, $fxRate);

    public function getLocalCurrency($companyId);
}
