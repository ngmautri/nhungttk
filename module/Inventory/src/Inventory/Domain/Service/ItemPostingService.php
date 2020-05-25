<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(ItemCmdRepositoryInterface $cmdRepository)
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
