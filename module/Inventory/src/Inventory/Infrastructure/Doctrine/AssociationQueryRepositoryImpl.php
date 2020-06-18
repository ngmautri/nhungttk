<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\Repository\AssociationQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationQueryRepositoryImpl extends AbstractDoctrineRepository implements AssociationQueryRepositoryInterface
{

    public function getVersion($id, $token = null)
    {}

    public function getVersionArray($id, $token = null)
    {}

    public function getRootEntityByTokenId($id, $token)
    {}
}
