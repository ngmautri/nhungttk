<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\Currency;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Company
{

    /**
     *
     * @var CompanyId
     */
    private $id;

    /**
     *
     * @var string
     */
    private $companyName;
    
    
   /**
    * 
    * @var Currency
    */
    private $localCurrency;

    /**
     *
     * @param CompanyId $id
     * @param string $companyName
     */
    public function __construct(CompanyId $id, $companyName, Currency $currency)
    {
        $this->id = $id;
        $this->companyName = $companyName;
        $this->currency = $currency;
    }
}
