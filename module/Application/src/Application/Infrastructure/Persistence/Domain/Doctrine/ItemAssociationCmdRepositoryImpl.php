<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\ItemAssociation\BaseAssociation;
use Application\Domain\Company\ItemAssociation\Repository\ItemAssociationCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAssociationCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemAssociationCmdRepositoryInterface
{

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryAssociation";

    public function removeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false)
    {}

    public function storeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false)
    {}
}
