<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\AccessControl\Repository\RoleQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RoleQueryRepositoryImpl extends AbstractDoctrineRepository implements RoleQueryRepositoryInterface

{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationAclRole";

    public function getById($id)
    {}

    public function getList(CompanySqlFilterInterface $filter)
    {}

    public function getRootByMemberId($memberId)
    {}

    public function getByUUID($uuid)
    {}
}