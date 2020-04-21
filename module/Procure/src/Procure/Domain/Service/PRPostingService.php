<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRPostingService
{

    protected $cmdRepository;

    public function __construct(PrCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new GrInvalidArgumentException("PR cmd repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
