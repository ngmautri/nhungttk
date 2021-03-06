<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use Inventory\Domain\Validator\AbstractInventorySpecificationFactory;
use Procure\Domain\Service\Contracts\FXServiceInterface;
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

    protected $fxService;

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    public function __construct(SharedSpecsFactory $sharedSpecsFactory, FXServiceInterface $fxService)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new InvalidArgumentException("Shared Specification is required");
        }

        if (! $fxService instanceof FXServiceInterface) {
            throw new InvalidArgumentException("FX service is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
        $this->fxService = $fxService;
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

    /**
     *
     * @return \Procure\Domain\Service\Contracts\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }
}

