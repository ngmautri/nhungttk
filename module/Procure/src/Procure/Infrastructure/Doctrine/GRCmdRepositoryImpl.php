<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Infrastructure\Mapper\GrMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRCmdRepositoryImpl extends AbstractDoctrineRepository implements GrCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtProcureGr";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtProcureGrRow";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericGR $rootEntity, GRRow $localEntity, $isPosting = false)
    {
        $isFlush = true;
        $increaseVersion = true;
        return $this->_storeRow($rootEntity, $localEntity, $isPosting, $isFlush, $increaseVersion);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $isFlush = true;
        $increaseVersion = true;
        return $this->_storeHeader($rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::store()
     */
    public function store(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericPO not retrieved.");
        }

        $rows = $rootEntity->getDocRows();

        if (count($rows) == null) {
            throw new InvalidArgumentException("Document is empty.");
        }

        $isFlush = true;
        $increaseVersion = true;
        $rootSnapShot = $this->_storeHeader($rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        $increaseVersion = false;
        $isFlush = false;
        foreach ($rows as $localEntity) {
            $this->_storeRow($rootEntity, $localEntity, $isPosting, $isFlush, $increaseVersion);
        }

        // it is time to flush.
        $this->getDoctrineEM()->flush();
        return $rootSnapShot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::post()
     */
    public function post(GenericGR $rootEntity, $generateSysNumber = True)
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
     * @param GenericGR $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @throws InvalidArgumentException
     */
    private function _storeHeader(GenericGR $rootEntity, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Generic GR not retrieved.");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRSnapshot $snapshot ;
         * @var \Application\Entity\NmtProcureGr $entity ;
         *     
         */

        $snapshot = $rootEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("Root Snapshot not created!");
        }

        if ($rootEntity->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());
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
        $entity = GrMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

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
     * @param GenericGR $rootEntity
     * @param GRRow $localEntity
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param int $n
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    private function _storeRow(GenericGR $rootEntity, GRRow $localEntity, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GR is empty");
        }

        if ($localEntity == null) {
            throw new InvalidArgumentException("GR row is empty");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGrRow $rowEntityDoctrine ;
         * @var GRRowSnapshot $snapshot ;
         * @var \Application\Entity\NmtProcureGr $rootEntityDoctrine ;
         */

        $rootEntityDoctrine = $this->doctrineEM->find("\Application\Entity\NmtProcureGr", $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("GR Entity not retrieved.");
        }

        $snapshot = $localEntity->makeSnapshot();

        if ($snapshot == null) {
            throw new InvalidArgumentException("GR row snapshot can not be created");
        }

        if ($localEntity->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException("Local Entity can't be retrieved.");
            }

            if ($rowEntityDoctrine->getGr() == null) {
                throw new InvalidArgumentException("Root entity is not valid");
            }

            if (! $rowEntityDoctrine->getGr()->getId() == $rootEntity->getId()) {
                throw new InvalidArgumentException(sprintf("Local row is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntity->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setGr($rootEntityDoctrine);
        }

        $rowEntityDoctrine = GrMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $snapshot, $rowEntityDoctrine);

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