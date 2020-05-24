<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\Service\Contracts\PostingServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(GrCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new GrInvalidArgumentException("GR cmd repository not set!");
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
