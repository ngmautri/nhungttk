<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\Currency;
use Application\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Company
{

    /**
     *
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

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
     * @var string
     */
    private $companyCode;

    /**
     *
     * @var TaxCode
     */
    private $taxCode;

    /**
     *
     * @var BusinessLicence
     */
    private $businessLicence;

    /**
     *
     * @var Currency
     */
    private $localCurrency;

    /**
     * *
     *
     * @var array
     */
    private $department;

    /**
     *
     * @var array
     */
    private $warehouse;

    /**
     *
     * @return \Application\Domain\Company\CompanyRepositoryInterface
     */
    protected function getCompanyRepository()
    {
        return $this->companyRepository;
    }

    /**
     *
     * @param \Application\Domain\Company\CompanyRepositoryInterface $companyRepository
     */
    protected function setCompanyRepository($companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     *
     * @param CompanyId $id
     * @param string $companyName
     */
    public function __construct(CompanyId $id, $companyName, Currency $currency)
    {
        $this->id = $id;

        if ($companyName == null) {
            throw new InvalidArgumentException("Company name is empty");
        }

        if ($currency == null) {
            throw new InvalidArgumentException("Currency is not set");
        }

        $this->companyName = $companyName;
        $this->currency = $currency;
    }

    public function createDepartment()
    {}

    public function createWarehouse()
    {}
}
