<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryItemVariant;
use Application\Entity\NmtInventoryItemVariantAttribute;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface;
use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;
use Inventory\Domain\Item\Variant\VariantAttributeSnapshot;
use Inventory\Domain\Item\Variant\VariantSnapshot;
use Inventory\Infrastructure\Mapper\ItemVariantMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemVariantCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemVariantCmdRepositoryInterface
{

    const VARIANT_ENTITY_NAME = "\Application\Entity\NmtInventoryItem";

    const VARIANT_ATTRIBUTE_ENTITY_NAME = "\Application\Entity\NmtInventoryItem";


    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::storeVariantCollection()
     */
    public function storeVariantCollection(GenericItem $rootEntity, $generateSysNumber = True)
    {
        $collection = $rootEntity->getVariantCollection();
        if($collection->isEmpty()){
            return;
        }

        foreach($collection as $localEntity){
            $this->storeWholeVariant($rootEntity, $localEntity);
        }
    }



    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::storeWholeVariant()
     */
    public function storeWholeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $isPosting = false;
        $entity = $this->_storeVariant($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

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
            $this->_storeVariantAttribute($entity, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        $this->doctrineEM->flush();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::storeVariant()
     */
    public function storeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $isPosting = false;

        $entity = $this->_storeVariant($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::storeAttribute()
     */
    public function storeAttribute(BaseVariant $rootEntity, $localEntity, $generateSysNumber = True)
    {
        $rootEntityDoctrine = $this->assertAndReturnVariant($rootEntity);
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $isPosting = false;
        $rowEntityDoctrine = $this->_storeVariantAttribute($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        $localSnapshot->id = $rowEntityDoctrine->getId();
        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::removeAttribute()
     */
    public function removeAttribute(BaseVariant $rootEntity, BaseVariantAttribute $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnVariant($rootEntity);

        $localSnapshot = $this->_getLocalSnapshot($localEntity);
        $rowEntityDoctrine = $this->assertAndReturnVariantAttribute($rootEntityDoctrine, $localSnapshot);

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }
    }

    /*
     * |=============================
     * | Assertion, Getter, Setter
     * |
     * |=============================
     */
    private function _storeVariantAttribute(NmtInventoryItemVariant $rootEntityDoctrine, VariantAttributeSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion)
    {
        $rowEntityDoctrine = $this->assertAndReturnVariantAttribute($rootEntityDoctrine, $localSnapshot);

        $rowEntityDoctrine = ItemVariantMapper::mapVariantAttributeEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        return $rowEntityDoctrine;
    }

    private function _storeVariant(VariantSnapshot $rootSnapshot, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::VARIANT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::VARIANT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ItemVariantMapper::mapVariantEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        return $entity;
    }

    private function _getRootSnapshot(BaseVariant $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    private function _getLocalSnapshot(BaseVariantAttribute $localEntity)
    {
        if (! $localEntity instanceof BaseVariantAttribute) {
            throw new InvalidArgumentException("Local entity not given!");
        }
        return $localEntity->makeSnapshot();
    }

    /**
     *
     * @param BaseVariant $rootEntity
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItemVariant
     */
    private function assertAndReturnVariant(BaseVariant $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseVariant not given.");
        }

        /**
         *
         * @var NmtInventoryItemVariant $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::VARIANT_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtInventoryItemVariant) {
            throw new InvalidArgumentException("Inventory Variant entity not found!");
        }

        return $rootEntityDoctrine;
    }

    /**
     *
     * @param NmtInventoryItemVariant $rootEntityDoctrine
     * @param VariantAttributeSnapshot $localSnapshot
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItemVariantAttribute
     */
    private function assertAndReturnVariantAttribute(NmtInventoryItemVariant $rootEntityDoctrine, VariantAttributeSnapshot $localSnapshot)
    {
        $rowEntityDoctrine = null;

        if ($localSnapshot == null) {
            throw new InvalidArgumentException(sprintf("VariantAttributeSnapshot not found! #%s", ""));
        }

        if ($localSnapshot->getId() > 0) {

            /**
             *
             * @var NmtInventoryItemVariantAttribute $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(self::VARIANT_ATTRIBUTE_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Variant attribute entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getVariant() == null) {
                throw new InvalidArgumentException("Attribute entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getVariant()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Variant attribute entity is corrupted! %s <> %s ", $rowEntityDoctrine->getVariant()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::VARIANT_ATTRIBUTE_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setVariant($rootEntityDoctrine);
            $rowEntityDoctrine = new $localClassName();
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create variant attribute  entity!");
        }

        return $rowEntityDoctrine;
    }

}