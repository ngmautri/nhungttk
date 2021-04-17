<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use Inventory\Domain\Validator\AbstractInventorySpecificationFactory;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractValidator
{

    protected $sharedSpecificationFactory;

    protected $domainSpecificationFactory;

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    public function __construct(SharedSpecsFactory $sharedSpecsFactory)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new InvalidArgumentException("Shared Specification is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
    }

    /**
     *
     * @return \Inventory\Domain\Validator\AbstractInventorySpecificationFactory
     */
    public function getDomainSpecificationFactory()
    {
        return $this->domainSpecificationFactory;
    }

    /**
     *
     * @param AbstractInventorySpecificationFactory $domainSpecificationFactory
     */
    public function setDomainSpecificationFactory(AbstractInventorySpecificationFactory $domainSpecificationFactory)
    {
        $this->domainSpecificationFactory = $domainSpecificationFactory;
    }
}

