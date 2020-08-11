<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehousePostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(WhCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Cmd Repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\PostingServiceInterface::getCmdRepository()
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
