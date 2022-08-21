<?php
namespace HR\Domain\Validator\Employee;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use HR\Domain\Service\HRDomainSpecificationFactory;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractIndividualValidator
{

    protected $sharedSpecificationFactory;

    protected $domainSpecificationFactory;

    /**
     *
     * @param SharedSpecsFactory $sharedSpecsFactory
     * @param HRDomainSpecificationFactory $domainSpecificationFactory
     * @throws InvalidArgumentException
     */
    public function __construct(SharedSpecsFactory $sharedSpecsFactory, HRDomainSpecificationFactory $domainSpecificationFactory = null)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new InvalidArgumentException("Shared Specification is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
        $this->domainSpecificationFactory = $domainSpecificationFactory;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    /**
     *
     * @return \HR\Domain\Service\HRDomainSpecificationFactory
     */
    public function getDomainSpecificationFactory()
    {
        return $this->domainSpecificationFactory;
    }
}

