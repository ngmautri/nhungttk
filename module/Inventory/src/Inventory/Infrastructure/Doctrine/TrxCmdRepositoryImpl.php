<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Domain\Shared\Constants;
use Application\Entity\NmtInventoryMv;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Inventory\Infrastructure\Mapper\TrxMapper;
use Procure\Domain\BaseRowSnapshot;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxCmdRepositoryImpl extends AbstractDoctrineRepository implements TrxCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryMv";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtInventoryTrx";

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericTrx $rootEntity, BaseRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var BaseRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Something wrong. Row Doctrine Entity not created");
        }

        $localSnapshot->id = $rowEntityDoctrine->getId();
        $localSnapshot->rowIdentifer = $rowEntityDoctrine->getRowIdentifier();
        $localSnapshot->docVersion = $rowEntityDoctrine->getDocVersion();
        $localSnapshot->revisionNo = $rowEntityDoctrine->getRevisionNo();

        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericTrx $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;

        /**
         *
         * @var \Application\Entity\NmtProcureQo $entity
         */
        $entity = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->sysNumber = $entity->getSysNumber();
        $rootSnapshot->docNumber = $entity->getDocNumber();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\QuotationRequest\Repository\QrCmdRepositoryInterface::store()
     */
    public function store(GenericTrx $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericTrx not retrieved.");
        }

        $rows = $rootEntity->getDocRows();

        if (count($rows) == null) {
            throw new InvalidArgumentException("Document is empty." . __FUNCTION__ . __LINE__);
        }

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;
        $rootEntityDoctrine = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Root doctrine entity not found.");
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($rows as $localEntity) {
            $n ++;

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $localSnapshot = $this->_getLocalSnapshot($localEntity);
            $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        $this->doctrineEM->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->docVersion = $rootEntityDoctrine->getDocVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::post()
     */
    public function post(GenericTrx $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        $rows = $rootEntity->getDocRows();

        if (count($rows) == null) {
            throw new InvalidArgumentException("Document is empty." . __FUNCTION__);
        }

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isPosting = true;
        $isFlush = true;
        $increaseVersion = true;

        $rootEntityDoctrine = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Root doctrine entity not found.");
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($rows as $localEntity) {
            $n ++;

            $localSnapshot = $this->_getLocalSnapshot($localEntity);

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n);
        }

        $this->getDoctrineEM()->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->docVersion = $rootEntityDoctrine->getDocVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * @param TrxSnapshot $rootSnapshot
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryMv
     */
    private function _storeHeader(TrxSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         *     
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = TrxMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

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

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $entity;
    }

    /**
     *
     * @param object $rootEntityDoctrine
     * @param BaseRowSnapshot $localSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param int $n
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryTrx
     */
    private function _storeRow($rootEntityDoctrine, BaseRowSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof NmtInventoryMv) {
            throw new InvalidArgumentException("Doctrine root (NmtInventoryMventity not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\NmtInventoryTrx $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            // update
            if ($rowEntityDoctrine->getMovement() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }
            // update
            if (! $rowEntityDoctrine->getMovement()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getQo()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setMovement($rootEntityDoctrine);
        }

        $rowEntityDoctrine = TrxMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        if ($n > 0) {
            $rowEntityDoctrine->setSysNumber(sprintf("%s-%s", $rootEntityDoctrine->getSysNumber(), $n));
        }

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $rowEntityDoctrine;
    }

    /**
     *
     * @param GenericTrx $rootEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\TrxSnapshot
     */
    private function _getRootSnapshot(GenericTrx $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var TrxSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof TrxSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param BaseRow $localEntity
     * @throws InvalidArgumentException
     * @return \Procure\Domain\BaseRowSnapshot
     */
    private function _getLocalSnapshot(BaseRow $localEntity)
    {
        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        /**
         *
         * @todo
         * @var BaseRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface::closeTrxOf()
     */
    public function closeTrxOfItem($itemId)
    {
        if ($itemId == null) {
            return;
        }

        $f = "UPDATE nmt_inventory_trx SET nmt_inventory_trx.doc_status='%s' WHERE nmt_inventory_trx.item_id=%s";
        $sql = sprintf($f, Constants::DOC_STATUS_CLOSED, $itemId);

        // echo $sql;
        try {
            $conn = $this->getDoctrineEM()->getConnection();
            return $conn->executeUpdate($sql);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface::closeTrxOf()
     */
    public function closeTrxOf($itemIds)
    {
        if (count($itemIds) == 0) {
            return;
        }

        $inString = '';
        $n = 0;

        foreach ($itemIds as $id) {
            $n ++;

            if ($n == 1) {
                $inString = $id;
            } else {
                $inString = $inString . ',' . $id;
            }
        }
        $inString = \sprintf('IN(%s)', $inString);

        $f = "UPDATE nmt_inventory_trx SET nmt_inventory_trx.doc_status='%s' WHERE nmt_inventory_trx.item_id %s";
        $sql = sprintf($f, Constants::DOC_STATUS_CLOSED, $inString);
        echo $sql;
        try {
            $conn = $this->getDoctrineEM()->getConnection();
            return $conn->executeUpdate($sql);
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function closeWhTrxOf($warehouseId, $itemIds)
    {
        if (count($itemIds) == 0 || $warehouseId == null) {
            return;
        }

        $inString = '';
        $n = 0;

        foreach ($itemIds as $id) {
            $n ++;

            if ($n == 1) {
                $inString = $id;
            } else {
                $inString = $inString . ',' . $id;
            }
        }
        $inString = \sprintf('IN(%s)', $inString);

        $f = "UPDATE nmt_inventory_trx SET nmt_inventory_trx.doc_status='%s' WHERE nmt_inventory_trx.wh_id=%s AND nmt_inventory_trx.item_id %s";
        $sql = sprintf($f, Constants::DOC_STATUS_CLOSED, $warehouseId, $inString);
        echo $sql;
        try {
            $conn = $this->getDoctrineEM()->getConnection();
            return $conn->executeUpdate($sql);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface::closeOtherWarehouseTrx()
     */
    public function closeOtherWarehouseTrx(GenericTrx $rootEntity, $itemIds)
    {
        if (count($itemIds) == 0 || $rootEntity == null) {
            return;
        }

        $inString = '';
        $n = 0;

        foreach ($itemIds as $id) {
            $n ++;

            if ($n == 1) {
                $inString = $id;
            } else {
                $inString = $inString . ',' . $id;
            }
        }

        $f = "UPDATE nmt_inventory_trx SET nmt_inventory_trx.doc_status='%s'";
        $f = $f . " WHERE nmt_inventory_trx.movement_id <> %s AND nmt_inventory_trx.wh_id=%s AND nmt_inventory_trx.item_id IN(%s)";
        $sql = sprintf($f, Constants::DOC_STATUS_CLOSED, $rootEntity->getId(), $rootEntity->getWarehouse(), $inString);
        echo $sql;
        try {
            $conn = $this->getDoctrineEM()->getConnection();
            return $conn->executeUpdate($sql);
        } catch (NoResultException $e) {
            return null;
        }
    }
}