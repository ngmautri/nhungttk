<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine;

use Application\Entity\NmtProcureClearingDoc;
use Application\Entity\NmtProcureClearingRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Clearing\BaseClearingDoc;
use Procure\Domain\Clearing\BaseClearingRow;
use Procure\Domain\Clearing\ClearingDocSnapshot;
use Procure\Domain\Clearing\ClearingRowSnapshot;
use Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\ClearingMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingCmdRepositoryImpl extends AbstractDoctrineRepository implements ClearingCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtProcureClearingDoc";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtProcureClearingRow";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface::storeRow()
     */
    public function storeRow(BaseClearingDoc $rootEntity, BaseClearingRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var ClearingRowSnapshot $localSnapshot ;
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
        $localSnapshot->rowIdentifer = $rowEntityDoctrine->getRowIdentifer();
        $localSnapshot->docVersion = $rowEntityDoctrine->getDocVersion();
        $localSnapshot->revisionNo = $rowEntityDoctrine->getRevisionNo();

        return $localSnapshot;
    }

    public function post(BaseClearingDoc $rootEntity, $generateSysNumber = TRUE)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        $rows = $rootEntity->getRowCollection();

        if ($rows->isEmpty()) {
            throw new InvalidArgumentException("Document is empty.");
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
            $localSnapshot = $this->_getLocalSnapshot($localEntity);
            $n ++;

            $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n);
        }

        // it is time to flush.
        $this->doctrineEM->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->version = $rootEntityDoctrine->getVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface::removeRow()
     */
    public function removeRow(BaseClearingDoc $rootEntity, BaseClearingRow $localEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var NmtProcureClearingDoc $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var NmtProcureClearingRow $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localEntity->getId()));
        }

        if ($rowEntityDoctrine->getDoc() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getDoc()->getId() != $rootEntity->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getDoc()->getId(), $rootEntity->getDoc()));
        }

        $isFlush = true;
        $increaseVersion = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(BaseClearingDoc $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity
         */
        $entity = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->sysNumber = $entity->getPrAutoNumber();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface::store()
     */
    public function store(BaseClearingDoc $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericPO not retrieved.");
        }

        $rowCollection = $rootEntity->getRowCollection();

        if ($rowCollection->isEmpty()) {
            throw new InvalidArgumentException("Document is empty.");
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
        foreach ($rowCollection as $localEntity) {

            $n ++;

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $localSnapshot = $this->_getLocalSnapshot($localEntity);
            $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        // it is time to flush.
        $this->getDoctrineEM()->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->version = $rootEntityDoctrine->getRevisionNo();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /*
     * |=============================
     * | HELPER FUNCTION
     * |=============================
     */
    private function _storeHeader(ClearingDocSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureClearingDoc $entity ;
         *     
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ClearingMapper::mapDocSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

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

    private function _storeRow($rootEntityDoctrine, ClearingRowSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof NmtProcureClearingDoc) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureClearingRow $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            if ($rowEntityDoctrine->getDoc() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            if (! $rowEntityDoctrine->getDoc()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getDoc()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setDoc($rootEntityDoctrine);
        }

        $rowEntityDoctrine = ClearingMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        if ($n > 0) {
            $rowEntityDoctrine->setRowIdentifer(sprintf("%s-%s", $rootEntityDoctrine->getSysNumber(), $n));
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

    private function _getLocalSnapshot(BaseClearingRow $localEntity)
    {
        if (! $localEntity instanceof BaseClearingRow) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        /**
         *
         * @todo
         * @var PRRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }

    private function _getRootSnapshot(BaseClearingDoc $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var ClearingDocSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }
}