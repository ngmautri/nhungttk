<?php
namespace Procure\Domain\Service;

use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;

/**
 * 
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrPostingService
{

    protected $cmdRepository;
    

    public function __construct(GrCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new GrInvalidArgumentException("PO cmd repository not set!");
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
