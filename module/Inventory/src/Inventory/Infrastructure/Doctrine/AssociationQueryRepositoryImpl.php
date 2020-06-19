<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\Repository\AssociationQueryRepositoryInterface;
use Inventory\Infrastructure\Mapper\AssociationMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationQueryRepositoryImpl extends AbstractDoctrineRepository implements AssociationQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Association\Repository\AssociationQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryAssociationItem $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryAssociationItem')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    public function getVersionArray($id, $token = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Association\Repository\AssociationQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'uuid' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryAssociationItem')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = AssociationMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = BaseAssociation::contructFromDB($rootSnapshot);
        return $rootEntity;
    }
}
