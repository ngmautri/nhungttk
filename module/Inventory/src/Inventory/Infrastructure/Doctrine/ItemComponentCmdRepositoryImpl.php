<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemComponent;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\CompositeItem;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Component\BaseComponent;
use Inventory\Domain\Item\Component\ComponentSnapshot;
use Inventory\Domain\Item\Repository\ItemComponentCmdRepositoryInterface;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Exception;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemComponentCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemComponentCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryItem";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtInventoryItemComponent";

    /*
     * |=============================
     * | Delegation
     * |
     * |=============================
     */
    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemComponentCmdRepositoryInterface::storeComponentCollection()
     */
    public function storeComponentCollection(GenericItem $rootEntity, $generateSysNumber = True)
    {
        if (! $rootEntity instanceof CompositeItem) {
            throw new Exception("Composite Item expected");
        }

        $collection = $rootEntity->getComponentCollection();
        if ($collection->isEmpty()) {
            return;
        }

        $n = 0;
        foreach ($collection as $localEntity) {
            $n ++;
            $this->storeComponenent($rootEntity, $localEntity, $generateSysNumber, $n);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemComponentCmdRepositoryInterface::storeComponenent()
     */
    public function storeComponenent(GenericItem $rootEntity, BaseComponent $localEntity, $generateSysNumber = True)
    {
        $rootEntityDoctrine = $this->assertAndReturnItem($rootEntity);
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $isPosting = false;
        $rowEntityDoctrine = $this->_storeComponent($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        $localSnapshot->id = $rowEntityDoctrine->getId();
        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemComponentCmdRepositoryInterface::removeComponent()
     */
    public function removeComponent(GenericItem $rootEntity, BaseComponent $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnItem($rootEntity);

        $localSnapshot = $this->_getLocalSnapshot($localEntity);
        $rowEntityDoctrine = $this->assertAndReturnItemComponent($rootEntityDoctrine, $localSnapshot);

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
    private function _storeComponent(NmtInventoryItem $rootEntityDoctrine, ComponentSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion)
    {
        $rowEntityDoctrine = $this->assertAndReturnItemComponent($rootEntityDoctrine, $localSnapshot);

        $rowEntityDoctrine = ItemMapper::mapItemComponentSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        return $rowEntityDoctrine;
    }

    private function _getRootSnapshot(GenericItem $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    private function _getLocalSnapshot(BaseComponent $localEntity)
    {
        if (! $localEntity instanceof BaseComponent) {
            throw new InvalidArgumentException("Local entity not given!");
        }
        return $localEntity->makeSnapshot();
    }

    /**
     *
     * @param GenericItem $rootEntity
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItem
     */
    private function assertAndReturnItem(GenericItem $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericItem not given.");
        }

        /**
         *
         * @var NmtInventoryItem $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(ItemComponentCmdRepositoryImpl::ROOT_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtInventoryItem) {
            throw new InvalidArgumentException("Inventory Variant entity not found!");
        }

        return $rootEntityDoctrine;
    }

    /**
     *
     * @param NmtInventoryItem $rootEntityDoctrine
     * @param ComponentSnapshot $localSnapshot
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItemComponent
     */
    private function assertAndReturnItemComponent(NmtInventoryItem $rootEntityDoctrine, ComponentSnapshot $localSnapshot)
    {
        $rowEntityDoctrine = null;

        if ($localSnapshot == null) {
            throw new InvalidArgumentException(sprintf("ComponentSnapshot not found! #%s", ""));
        }

        if ($localSnapshot->getId() > 0) {

            /**
             *
             * @var NmtInventoryItemComponent $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(ItemComponentCmdRepositoryImpl::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Item component entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getParent() == null) {
                throw new InvalidArgumentException("Attribute entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getParent()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Variant attribute entity is corrupted! %s <> %s ", $rowEntityDoctrine->getParent()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = ItemComponentCmdRepositoryImpl::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();
            $rowEntityDoctrine->setParent($rootEntityDoctrine); // To update, important.
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create variant attribute  entity!");
        }

        return $rowEntityDoctrine;
    }
}