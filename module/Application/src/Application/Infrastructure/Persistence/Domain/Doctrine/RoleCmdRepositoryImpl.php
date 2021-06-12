<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\AccessControl\BaseRole;
use Application\Domain\Company\AccessControl\Repository\RoleCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RoleCmdRepositoryImpl extends AbstractDoctrineRepository implements RoleCmdRepositoryInterface
{

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationAclRole";

    public function removeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false)
    {}

    public function storeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false)
    {}
}
