<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Association\Repository\AssociationCmdRepositoryInterface;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(AssociationCmdRepositoryInterface $cmdRepository)
    {
        if (! $cmdRepository instanceof AssociationCmdRepositoryInterface) {
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
