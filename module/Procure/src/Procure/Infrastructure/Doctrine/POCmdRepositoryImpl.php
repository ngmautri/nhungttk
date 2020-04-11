<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;
use Procure\Infrastructure\Mapper\PoMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POCmdRepositoryImpl extends AbstractDoctrineRepository implements POCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtProcurePo";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtProcurePoRow";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericPO $rootEntity, PORow $localEntity, $isPosting = false)
    {
        $isFlush = true;
        $increaseVersion = true;
        return $this->_storeRow($rootEntity, $localEntity, $isPosting, $isFlush, $increaseVersion);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::post()
     */
    public function post(GenericPO $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Generic GR not given.");
        }

        if ($rootEntity->getId() == null) {
            throw new InvalidArgumentException("Entity ID not found.");
        }

        $rows = $rootEntity->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("Document is empty");
        }

        $isPosting = true;
        $isFlush = true;
        $increaseVersion = true;

        $rootSnapshot = $this->_storeHeader($rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        $increaseVersion = false;
        $n = 0;
        foreach ($rows as $localEntity) {
            $n ++;
            $this->_storeRow($rootEntity, $localEntity, $isPosting, $isFlush, $increaseVersion, $n);
        }

        // it is time to flush.
        $this->doctrineEM->flush();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $isFlush = true;
        $increaseVersion = true;
        return $this->_storeHeader($rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::store()
     */
    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericPO not retrieved.");
        }

        $rows = $rootEntity->getDocRows();

        if (count($rows) == null) {
            throw new InvalidArgumentException("Document is empty.");
        }

        $isFlush = false;
        $increaseVersion = true;
        $rootSnapShot = $this->_storeHeader($rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        $increaseVersion = false;
        foreach ($rows as $localEntity) {
            $this->_storeRow($rootEntity, $localEntity, $isPosting, $isFlush, $increaseVersion);
        }

        // it is time to flush.
        $this->getDoctrineEM()->flush();
        return $rootSnapShot;
    }

    /**
     *
     * @param GenericPO $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\POSnapshot
     */
    private function _storeHeader(GenericPO $rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Generic PO not retrieved.");
        }

        /**
         *
         * @var \Procure\Domain\PurchaseOrder\POSnapshot $snapshot ;
         * @var \Application\Entity\NmtProcurePo $entity ;
         *     
         */
        $snapshot = $rootEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("Root Snapshot not created!");
        }

        if ($rootEntity->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(Self::ROOT_ENTITY_NAME, $rootEntity->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Entity not found. %s", $rootEntity->getId()));
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
        $entity = PoMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootEntity->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        $snapshot->id = $entity->getId();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->sysNumber = $entity->getSysNumber();
        return $snapshot;
    }

    /**
     *
     * @param GenericPO $rootEntity
     * @param PORow $localEntity
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param boolean $n
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    private function _storeRow(GenericPO $rootEntity, PORow $localEntity, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity is empty");
        }

        if ($localEntity == null) {
            throw new InvalidArgumentException("Local row is empty");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePoRow $rowEntityDoctrine ;
         * @var PORowSnapshot $snapshot ;
         * @var \Application\Entity\NmtProcurePo $rootEntityDoctrine ;
         */

        $rootEntityDoctrine = $this->doctrineEM->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Root Entity not retrieved.");
        }

        $snapshot = $localEntity->makeSnapshot();

        if ($snapshot == null) {
            throw new InvalidArgumentException("Root snapshot can not be created");
        }

        if ($localEntity->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException("Local Entity can't be retrieved.");
            }

            /**
             *
             * @todo update
             */
            if ($rowEntityDoctrine->getPo() == null) {
                throw new InvalidArgumentException("Root entity is not valid");
            }

            /**
             *
             * @todo update
             */
            if (! $rowEntityDoctrine->getPo()->getId() == $rootEntity->getId()) {
                throw new InvalidArgumentException(sprintf("Local row is corrupted! %s <> %s ", $rowEntityDoctrine->getPo()->getId(), $rootEntity->getId()));
            }
        } else {

            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setPO($rootEntityDoctrine);
        }

        $rowEntityDoctrine = PoMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $snapshot, $rowEntityDoctrine);

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

        if ($snapshot->getId() == null) {
            $snapshot->id = $rowEntityDoctrine->getId();
        }
        $snapshot->rowIdentifer = $rowEntityDoctrine->getRowIdentifer();

        return $snapshot;
    }
}
