<?php
namespace Procure\Domain\Validator;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use Procure\Domain\Service\Contracts\FXServiceInterface;
use Procure\Domain\Shared\Specification\AbstractSpecificationFactory as ProcureSpecsFactory;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractValidator
{

    protected $sharedSpecificationFactory;

    protected $procureSpecificationFactory;

    protected $fxService;

    /**
     *
     * @param SharedSpecsFactory $sharedSpecsFactory
     * @param FXServiceInterface $fxService
     * @param ProcureSpecsFactory $procureSpecsFactory
     * @throws \InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function __construct(SharedSpecsFactory $sharedSpecsFactory, FXServiceInterface $fxService, ProcureSpecsFactory $procureSpecsFactory = null)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new \InvalidArgumentException("Shared Specification is required");
        }

        if (! $fxService instanceof FXServiceInterface) {
            throw new InvalidArgumentException("FX service is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
        $this->procureSpecificationFactory = $procureSpecsFactory;
        $this->fxService = $fxService;
    }

    /**
     *
     * @return \Procure\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getProcureSpecificationFactory()
    {
        return $this->procureSpecificationFactory;
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
     * @return \Procure\Domain\Service\Contracts\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }
}

