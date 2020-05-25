<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Service\Contracts\FXServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractValidator
{

    protected $sharedSpecificationFactory;

    protected $fxService;

    public function __construct(SharedSpecsFactory $sharedSpecsFactory, FXServiceInterface $fxService)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new PoInvalidArgumentException("Shared Specification is required");
        }

        if (! $fxService instanceof FXServiceInterface) {
            throw new PoInvalidArgumentException("FX service is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
        $this->fxService = $fxService;
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

