<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POPostingService
{

    protected $cmdRepository;

    public function __construct(POCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("PO cmd repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
