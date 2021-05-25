<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\ItemAttribute\Factory\ItemAttributeFactory;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Helper\CompanyCollectionHelper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemAttributeMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeQueryRepositoryImpl extends AbstractDoctrineRepository implements ItemAttributeQueryRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryAttributeGroup";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtInventoryAttribute";

    public function getRootByMemberId($memberId)
    {
        $criteria = array(
            'id' => $memberId
        );

        $doctrineEntity = $this->doctrineEM->getRepository(self::ROOT_ENTITY_NAME)->findOneBy($criteria);
        if ($doctrineEntity == null) {
            throw new \InvalidArgumentException("Not Found id" . $memberId);
        }

        if ($doctrineEntity->get() != null) {
            return $doctrineEntity->getPr()->getId();
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartQueryRepositoryInterface::getList()
     */
    public function getList(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        $doctrineEM = $this->getDoctrineEM();
        $rootEntityDoctrine = $doctrineEM->getRepository(self::ROOT_ENTITY_NAME)->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemAttributeMapper::createAttributeGroupSnapshot($doctrineEM, $rootEntityDoctrine);
        $rootEntity = ItemAttributeFactory::contructFromDB($rootSnapshot);

        $rootEntity->setAttributeCollectionRef(CompanyCollectionHelper::createItemAttributeCollectionRef($doctrineEM, $id));
        return $rootEntity;
    }

    public function getByUUID($uuid)
    {}
}