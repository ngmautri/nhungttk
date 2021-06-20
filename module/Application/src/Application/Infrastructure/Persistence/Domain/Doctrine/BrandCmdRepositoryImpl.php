<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\Repository\BrandCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandCmdRepositoryImpl extends AbstractDoctrineRepository implements BrandCmdRepositoryInterface
{

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationAclRole";

    public function removeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false)
    {}

    public function storeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false)
    {}
}
