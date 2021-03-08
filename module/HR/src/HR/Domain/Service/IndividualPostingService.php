<?php
namespace HR\Domain\Service;

use HR\Domain\Employee\Repository\IndividualCmdRepositoryInterface;
use HR\Domain\Service\Contracts\PostingServiceInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(IndividualCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Cmd Repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\PostingServiceInterface::getCmdRepository()
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
