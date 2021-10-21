<?php
namespace Procure\Domain\Service;

use Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface;
use Procure\Domain\Service\Contracts\PostingServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingDocPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(ClearingCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new \InvalidArgumentException("Clearing CMD repository not set!");
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
