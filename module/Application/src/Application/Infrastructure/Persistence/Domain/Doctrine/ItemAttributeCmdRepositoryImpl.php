<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface;
use Application\Entity\AppCoa;
use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtInventoryAttribute;
use Application\Entity\NmtInventoryAttributeGroup;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ChartMapper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemAttributeMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemAttributeCmdRepositoryInterface
{

    const COMPANNY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ATTRIBUTE_GROUP_ENTITY_NAME = "\Application\Entity\NmtInventoryAttributeGroup";

    const ATTRIBUTE_ENTITY_NAME = "\Application\Entity\NmtInventoryAttribute";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::store()
     */
    public function storeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeAttributeGroup($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface::storeWholeAttributeGroup()
     */
    public function storeWholeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeAttributeGroup($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        $collection = $localEntity->getAttributeCollection();
        if ($collection->isEmpty()) {
            return $rootSnapshot;
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($collection as $attributeEntity) {
            $n ++;

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $localSnapshot = $this->_getLocalSnapshot($attributeEntity);
            $this->_storeAttribute($entity, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        $this->doctrineEM->flush();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface::storeAttribute()
     */
    public function storeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnAttributeGroup($rootEntity);
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeAttribute($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        $localSnapshot->id = $rowEntityDoctrine->getId();
        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface::removeAttribute()
     */
    public function removeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnAttributeGroup($rootEntity);

        $localSnapshot = $this->_getLocalSnapshot($localEntity);
        $rowEntityDoctrine = $this->assertAndReturnAttribute($rootEntityDoctrine, $localSnapshot);

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    public function removeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {}

    private function _storeAttribute(AppCoa $rootEntityDoctrine, AccountSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion)
    {
        $rowEntityDoctrine = $this->assertAndReturnAccount($rootEntityDoctrine, $localSnapshot);

        $rowEntityDoctrine = ChartMapper::mapAccountEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        return $rowEntityDoctrine;
    }

    /**
     *
     * @param AttributeGroupSnapshot $rootSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryAttributeGroup
     */
    private function _storeAttributeGroup(AttributeGroupSnapshot $rootSnapshot, $isPosting, $isFlush, $increaseVersion)
    {

        /**
         *
         * @var \Application\Entity\NmtInventoryAttributeGroup $entity ;
         *
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ATTRIBUTE_GROUP_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::ATTRIBUTE_GROUP_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ItemAttributeMapper::mapAttributeGroupEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        return $entity;
    }

    /**
     *
     * @param BaseAttributeGroup $rootEntity
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeGroupSnapshot
     */
    private function _getRootSnapshot(BaseAttributeGroup $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    private function _getLocalSnapshot(BaseAttribute $localEntity)
    {
        if (! $localEntity instanceof BaseAttribute) {
            throw new InvalidArgumentException("Local entity not given!");
        }
        return $localEntity->makeSnapshot();
    }

    /**
     *
     * @param BaseCompany $rootEntity
     * @throws InvalidArgumentException
     */
    private function assertAndReturnCompany(BaseCompany $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseCompany not given.");
        }

        /**
         *
         * @var NmtApplicationCompany $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(ItemAttributeCmdRepositoryImpl::COMPANNY_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtApplicationCompany) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        return $rootEntityDoctrine;
    }

    private function assertAndReturnAttributeGroup(BaseAttributeGroup $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseAttributeGroup not given.");
        }

        /**
         *
         * @var NmtInventoryAttributeGroup $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ATTRIBUTE_GROUP_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtInventoryAttributeGroup) {
            throw new InvalidArgumentException("Nmt Inventory Attribute Group entity not found!");
        }

        return $rootEntityDoctrine;
    }

    private function assertAndReturnAttribute(NmtInventoryAttribute $rootEntityDoctrine, BaseAttributeSnapshot $localSnapshot)
    {
        $rowEntityDoctrine = null;

        if ($localSnapshot == null) {
            throw new InvalidArgumentException(sprintf("BaseAttributeSnapshot not found! #%s", ""));
        }

        if ($localSnapshot->getId() > 0) {

            /**
             *
             * @var NmtInventoryAttribute $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(self::ATTRIBUTE_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Attribute entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getGroup() == null) {
                throw new InvalidArgumentException("Attribute entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getGroup()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Attribute entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGroup()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::ATTRIBUTE_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setGroup($rootEntityDoctrine);
            $rowEntityDoctrine = new $localClassName();
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create account  entity!");
        }

        return $rowEntityDoctrine;
    }
}
