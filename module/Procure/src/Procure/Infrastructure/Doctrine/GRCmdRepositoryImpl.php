<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcureGrRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshot;
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
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::removeRow()
     */
    public function removeRow(GenericGR $rootEntity, GRRow $localEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var NmtProcureGr $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var NmtProcureGrRow $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localEntity->getId()));
        }

        if ($rowEntityDoctrine->getPr() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getGr()->getId() != $rootEntity->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntity->getId()));
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
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericGR $rootEntity, GRRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var GRRowSnapshot $localSnapshot ;
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->docVersion = $entity->getDocVersion();
        $rootSnapshot->sysNumber = $entity->getSysNumber();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();

        return $rootSnapshot;
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

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;
        $rootEntityDoctrine = $this->_storeHeader($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Root doctrine entity not found.");
        }

        $increaseVersion = false;
        $isFlush = false;

        foreach ($rows as $localEntity) {
            $localSnapshot = $this->_getLocalSnapshot($localEntity);
            $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        // it is time to flush.
        $this->getDoctrineEM()->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->docVersion = $rootEntityDoctrine->getDocVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::post()
     */
    public function post(GenericGR $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        $rows = $rootEntity->getDocRows();

        if (count($rows) == null) {
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
        $rootSnapshot->docVersion = $rootEntityDoctrine->getDocVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * @param GRSnapshot $rootSnapshot
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtProcureGr
     */
    private function _storeHeader(GRSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $entity ;
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
        $entity = GrMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

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
     * @param GRRow $localEntity
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param int $n
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtProcureGrRow
     */
    private function _storeRow($rootEntityDoctrine, GRRowSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof NmtProcureGr) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGrRow $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            if ($rowEntityDoctrine->getGr() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            if (! $rowEntityDoctrine->getGr()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
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

        $rowEntityDoctrine = GrMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

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

    /**
     *
     * @param GenericGR $rootEntity
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRSnapshot
     */
    private function _getRootSnapshot(GenericGR $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var GRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param GRRow $localEntity
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    private function _getLocalSnapshot(GRRow $localEntity)
    {
        if (! $localEntity instanceof GRRow) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        /**
         *
         * @todo
         * @var GRRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }
}