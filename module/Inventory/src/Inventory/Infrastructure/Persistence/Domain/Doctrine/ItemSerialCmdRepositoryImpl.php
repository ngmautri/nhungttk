<?php
namespace Inventory\Infrastructure\Persistence\Domain\Doctrine;

use Application\Entity\NmtInventoryItemSerial;
use Application\Entity\NmtInventoryItemVariant;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\Serial\BaseSerial;
use Inventory\Domain\Item\Serial\GenericSerial;
use Inventory\Domain\Item\Serial\SerialSnapshot;
use Inventory\Domain\Item\Serial\Repository\ItemSerialCmdRepositoryInterface;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemSerialMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemSerialCmdRepositoryInterface
{

    const SERIAL_ENTITY_NAME = "\Application\Entity\NmtInventoryItemSerial";

    /*
     * |=============================
     * | Delegation
     * |
     * |=============================
     */
    public function storeSerial(GenericSerial $rootEntity, $generateSysNumber = True)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $isPosting = false;

        $entity = $this->_storeSerial($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        return $rootSnapshot;
    }

    public function storeSerialCollection($collection, $generateSysNumber = True)
    {
        if ($collection->isEmpty()) {
            return;
        }

        $n = 0;
        foreach ($collection as $rootEntity) {
            $n ++;
            $this->storeSerial($rootEntity, $generateSysNumber);
        }
    }

    public function removeSerial(GenericSerial $rootEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnSerial($rootEntity);

        $isFlush = true;

        $this->getDoctrineEM()->remove($rootEntityDoctrine);

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

    /**
     *
     * @param SerialSnapshot $rootSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param boolean $sysNumber
     * @throws InvalidArgumentException
     * @return NULL|\Application\Entity\NmtInventoryItemSerial
     */
    private function _storeSerial(SerialSnapshot $rootSnapshot, $isPosting, $isFlush, $increaseVersion, $sysNumber = null)
    {
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::SERIAL_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::SERIAL_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ItemSerialMapper::mapSerialEntity($this->getDoctrineEM(), $rootSnapshot, $entity);
        $entity->setSysNumber($sysNumber);

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
     * @param BaseSerial $rootEntity
     * @throws InvalidArgumentException
     * @return object
     */
    private function _getRootSnapshot(BaseSerial $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    /**
     *
     * @param BaseSerial $rootEntity
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryItemVariant
     */
    private function assertAndReturnSerial(BaseSerial $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseSerial not given.");
        }

        /**
         *
         * @var NmtInventoryItemVariant $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::SERIAL_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtInventoryItemSerial) {
            throw new InvalidArgumentException("Inventory Serial entity not found!");
        }

        return $rootEntityDoctrine;
    }
}