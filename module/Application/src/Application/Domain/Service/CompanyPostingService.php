<?php
namespace Application\Domain\Service;

use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Service\Contracts\PostingServiceInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyPostingService implements PostingServiceInterface
{

    protected $cmdRepository;

    public function __construct(CompanyCmdRepositoryInterface $cmdRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("Company CMD repository not set!");
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
