<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(TrxCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Cmd Repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Contracts\PostingServiceInterface::getCmdRepository()
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
