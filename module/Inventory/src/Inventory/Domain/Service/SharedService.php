<?php
namespace Inventory\Domain\Service;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Procure\Domain\Service\Contracts\FXServiceInterface;
use InvalidArgumentException;

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

    protected $postingService;

    protected $valuationService;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param FXServiceInterface $fxService
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, FXServiceInterface $fxService, PostingServiceInterface $postingService)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($fxService == null) {
            throw new InvalidArgumentException("FX service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->fxService = $fxService;
        $this->postingService = $postingService;
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

    /**
     *
     * @return \Inventory\Domain\Service\Contracts\PostingServiceInterface
     */
    public function getPostingService()
    {
        return $this->postingService;
    }

    /**
     *
     * @return \Inventory\Domain\Service\TrxValuationService
     */
    public function getValuationService()
    {
        return $this->valuationService;
    }

    /**
     *
     * @param TrxValuationService $valuationService
     */
    public function setValuationService(TrxValuationService $valuationService)
    {
        $this->valuationService = $valuationService;
    }
}
