<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrPostingService
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
     * @return \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
