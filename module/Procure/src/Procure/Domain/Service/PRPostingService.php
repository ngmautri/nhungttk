<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Domain\Service\Contracts\PostingServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(PrCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("PR cmd repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\PostingServiceInterface::getCmdRepository()
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
