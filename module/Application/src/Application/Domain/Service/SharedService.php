<?php
namespace Application\Domain\Service;

use Application\Domain\Service\Contracts\PostingServiceInterface;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Validator\AbstractInventorySpecificationFactory;
use Procure\Domain\Service\Contracts\FXServiceInterface;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

/**
 * Application Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SharedService implements SharedServiceInterface
{

    protected $sharedSpecificationFactory;

    protected $domainSpecificationFactory;

    protected $postingService;

    protected $valuationService;

    protected $logger;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param FXServiceInterface $fxService
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, PostingServiceInterface $postingService)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->postingService = $postingService;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedServiceInterface::getDomainSpecificationFactory()
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
}
