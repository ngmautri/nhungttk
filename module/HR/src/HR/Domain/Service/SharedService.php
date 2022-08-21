<?php
namespace HR\Domain\Service;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use HR\Domain\Service\Contracts\PostingServiceInterface;
use HR\Domain\Service\Contracts\SharedServiceInterface;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

/**
 * HR Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SharedService implements SharedServiceInterface
{

    protected $sharedSpecificationFactory;

    protected $domainSpecificationFactory;

    protected $postingService;

    protected $logger;

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
     * @param HRDomainSpecificationFactory $domainSpecificationFactory
     */
    public function setDomainSpecificationFactory(HRDomainSpecificationFactory $domainSpecificationFactory)
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
     * @return \Inventory\Domain\Service\Contracts\PostingServiceInterface
     */
    public function getPostingService()
    {
        return $this->postingService;
    }
}
