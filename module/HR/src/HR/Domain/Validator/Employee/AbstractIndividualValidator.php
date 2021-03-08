<?php
namespace HR\Domain\Validator\Employee;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use InvalidArgumentException;
use HR\Domain\Validator\AbstractHrSpecificationFactory;

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
     * @param \Application\Domain\Shared\Specification\AbstractSpecificationFactory $sharedSpecsFactory
     * @throws \Procure\Domain\Exception\PoInvalidArgumentException
     */
    public function __construct(SharedSpecsFactory $sharedSpecsFactory, AbstractHrSpecificationFactory $domainSpecificationFactory = null)
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
     * @return \HR\Domain\Validator\AbstractHrSpecificationFactory
     */
    public function getDomainSpecificationFactory()
    {
        return $this->domainSpecificationFactory;
    }
}

