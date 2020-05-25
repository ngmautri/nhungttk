<?php
namespace Procure\Domain\Service;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Procure\Domain\Service\Contracts\FXServiceInterface;
use Procure\Domain\Service\Contracts\PostingServiceInterface;
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

    /**
     *
     * @return \Procure\Domain\Service\Contracts\PostingServiceInterface
     */
    public function getPostingService()
    {
        return $this->postingService;
    }

    /**
     *
     * @param \Procure\Domain\Service\Contracts\PostingServiceInterface $postingService
     */
    public function setPostingService($postingService)
    {
        $this->postingService = $postingService;
    }

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param FXServiceInterface $fxService
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, FXServiceInterface $fxService, PostingServiceInterface $postingService = null)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($fxService == null) {
            throw new InvalidArgumentException("FX service not found");
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
}
