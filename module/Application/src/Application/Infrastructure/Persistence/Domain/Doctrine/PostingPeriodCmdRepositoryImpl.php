<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\PostingPeriod\BasePostingPeriod;
use Application\Domain\Company\PostingPeriod\Repository\PostingPeriodCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostingPeriodCmdRepositoryImpl extends AbstractDoctrineRepository implements PostingPeriodCmdRepositoryInterface
{

    const COMPANNY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtFinPostingPeriod";

    public function removePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false)
    {}

    public function storePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false)
    {}
}
