<?php
namespace Procure\Domain\Service;

use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;

/**
 *
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APPostingService
{
    
    protected $cmdRepository;
    
    
    public function __construct(APCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new GrInvalidArgumentException("AP cmd repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }
    
   /**
    * 
    * @return \Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface
    */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
