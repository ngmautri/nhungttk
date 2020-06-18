<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\Repository\AssociationCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationCmdRepositoryImpl extends AbstractDoctrineRepository implements AssociationCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryAssociationItem";

    public function store(BaseAssociation $rootEntity, $generateSysNumber = True)
    {}
}