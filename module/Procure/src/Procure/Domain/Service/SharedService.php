<?php
namespace Procure\Domain\Service;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Service\Contracts\FXServiceInterface;

/**
 * PO Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SharedService
{

    protected $sharedSpecificationFactory;

    protected $fxService;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param FXServiceInterface $fxService
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, FXServiceInterface $fxService)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($fxService == null) {
            throw new InvalidArgumentException("FX service not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->fxService = $fxService;
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
