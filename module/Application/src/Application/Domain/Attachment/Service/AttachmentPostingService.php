<?php
namespace Application\Domain\Attachment\Service;

use Application\Domain\Attachment\Repository\AttachmentCmdRepositoryInterface;
use Application\Domain\Service\Contracts\PostingServiceInterface;
use InvalidArgumentException;

/**
 * Posi
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(AttachmentCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Attachment CMD repository not set!");
        }
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\PostingServiceInterface::getCmdRepository()
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
}
