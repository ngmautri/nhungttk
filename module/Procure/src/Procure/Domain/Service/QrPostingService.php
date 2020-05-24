<?php
namespace Procure\Domain\Service;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface;
use Procure\Domain\Service\Contracts\PostingServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(QrCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Quotation cmd repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * @return \Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
