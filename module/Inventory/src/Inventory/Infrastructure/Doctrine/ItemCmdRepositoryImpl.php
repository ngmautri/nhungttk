<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryItem;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\BaseItem;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Component\BaseComponent;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\Repository\ItemComponentCmdRepositoryInterface;
use Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface;
use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryItem";

    private $itemVariantRepository;

    private $itemComponentRepository;

    /*
     * |=============================
     * | Delegation
     * |
     * |=============================
     */

    // +++++++++++++++++++++
    // Component
    // +++++++++++++++++++++
    public function storeComponentCollection(GenericItem $rootEntity, $generateSysNumber = True)
    {
        $this->assertItemComponentRepository();
        return $this->getItemComponentRepository()->storeComponentCollection($rootEntity, $generateSysNumber);
    }

    public function storeComponenent(GenericItem $rootEntity, BaseComponent $localEntity, $generateSysNumber = True)
    {
        $this->assertItemComponentRepository();
        return $this->getItemComponentRepository()->storeComponenent($rootEntity, $localEntity, $generateSysNumber);
    }

    public function removeComponent(GenericItem $rootEntity, BaseComponent $localEntity, $isPosting = false)
    {
        $this->assertItemComponentRepository();
        return $this->getItemComponentRepository()->removeComponent($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Variant
    // +++++++++++++++++++++

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface::storeVariantCollection()
     */
    public function storeVariantCollection(GenericItem $rootEntity, $generateSysNumber = True)
    {
        $this->assertItemVariantRepository();
        return $this->getItemVariantRepository()->storeVariantCollection($rootEntity);
    }

    public function storeWholeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True)
    {
        $this->assertItemVariantRepository();
        return $this->getItemVariantRepository()->storeWholeVariant($rootEntity, $localEntity, $generateSysNumber);
    }

    public function storeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True)
    {
        $this->assertItemVariantRepository();
        return $this->getItemVariantRepository()->storeVariant($rootEntity, $localEntity, $generateSysNumber);
    }

    public function storeAttribute(BaseVariant $rootEntity, $localEntity, $generateSysNumber = True)
    {
        $this->assertItemVariantRepository();
        return $this->getItemVariantRepository()->storeAttribute($rootEntity, $localEntity, $generateSysNumber);
    }

    public function removeAttribute(BaseVariant $rootEntity, BaseVariantAttribute $localEntity, $isPosting = false)
    {
        $this->assertItemVariantRepository();
        return $this->getItemVariantRepository()->removeAttribute($rootEntity, $localEntity);
    }

    /*
     * |=============================
     * | Item
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface::store()
     */
    public function store(GenericItem $rootEntity, $generateSysNumber = True)
    {
        Assert::notNull($rootEntity, "GenericItem not retrieved.");
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $increaseVersion = false;

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         *     
         */
        if ($rootEntity->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootEntity->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }

            $increaseVersion = true;
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ItemMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootSnapshot->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->sysNumber = $entity->getSysNumber();
        return $rootSnapshot;
    }

    /*
     * |=================================
     * | Assert underlying repository
     * |
     * |==================================
     */
    private function assertItemVariantRepository()
    {
        if ($this->getItemVariantRepository() == null) {
            throw new InvalidArgumentException("Item Variant repository is not found!");
        }
    }

    private function assertItemComponentRepository()
    {
        if ($this->getItemComponentRepository() == null) {
            throw new InvalidArgumentException("Item Component repository is not found!");
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface::getItemComponentRepository()
     */
    public function getItemComponentRepository()
    {
        if ($this->itemComponentRepository == null) {
            throw new InvalidArgumentException("Item Component repository is not found!");
        }
        return $this->itemComponentRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Repository\ItemVariantCmdRepositoryInterface
     */
    public function getItemVariantRepository()
    {
        if ($this->itemVariantRepository == null) {
            throw new InvalidArgumentException("Item Variant repository is not found!");
        }
        return $this->itemVariantRepository;
    }

    /*
     * |=================================
     * | Setter and getter
     * |
     * |==================================
     */

    /**
     *
     * @param BaseItem $rootEntity
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItem|object|NULL
     */
    private function assertAndReturnItem(BaseItem $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseCompany not given.");
        }

        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if (! $rootEntityDoctrine instanceof NmtInventoryItem) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        return $rootEntityDoctrine;
    }

    /**
     *
     * @param GenericItem $rootEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    private function _getRootSnapshot(GenericItem $rootEntity)
    {
        /**
         *
         * @var ItemSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof ItemSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param ItemVariantCmdRepositoryInterface $itemVariantRepository
     */
    public function setItemVariantRepository(ItemVariantCmdRepositoryInterface $itemVariantRepository)
    {
        $this->itemVariantRepository = $itemVariantRepository;
    }

    /**
     *
     * @param ItemComponentCmdRepositoryInterface $itemComponentRepository
     */
    public function setItemComponentRepository(ItemComponentCmdRepositoryInterface $itemComponentRepository)
    {
        $this->itemComponentRepository = $itemComponentRepository;
    }
}