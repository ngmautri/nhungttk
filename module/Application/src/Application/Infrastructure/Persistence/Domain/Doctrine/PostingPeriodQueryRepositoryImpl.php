<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\PostingPeriod\Repository\PostingPeriodQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostingPeriodQueryRepositoryImpl extends AbstractDoctrineRepository implements PostingPeriodQueryRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtFinPostingPeriod";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtInventoryAttribute";

    public function getById($id)
    {}

    public function getList(CompanySqlFilterInterface $filter)
    {}

    public function getRootByMemberId($memberId)
    {}

    public function getByUUID($uuid)
    {}
}