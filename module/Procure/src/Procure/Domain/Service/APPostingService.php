<?php
namespace Procure\Domain\Service;

use Procure\Domain\APInvoice\APDocCmdRepositoryInterface;
use Procure\Domain\APInvoice\APDocQueryRepositoryInterface;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APPostingService
{

    protected $apDocCmdRepository;

    protected $apDocQueryRepository;

    public function __construct(APDocCmdRepositoryInterface $apDocCmdRepository, APDocQueryRepositoryInterface $apDocQueryRepository)
    {
        if ($apDocCmdRepository == null) {
            throw new InvalidArgumentException("AP Doc cmd repository not set!");
        }

        if ($apDocQueryRepository == null) {
            throw new InvalidArgumentException("AP Doc query repository not set!");
        }

        $this->apDocCmdRepository = $apDocCmdRepository;
        $this->apDocCmdRepository = $apDocQueryRepository;
    }
    
    
    public function getApDocCmdRepository()
    {
        return $this->apDocCmdRepository;
    }

    public function getApDocQueryRepository()
    {
        return $this->apDocQueryRepository;
    }


   
}
