<?php
namespace Procure\Domain\Service;

use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Service\Contracts\PostingServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(APCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new GrInvalidArgumentException("AP CMD repository not set!");
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
