<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Entity\NmtProcurePr;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Infrastructure\Mapper\PrMapper;
use Application\Entity\NmtProcurePrRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PRCmdRepositoryImpl extends AbstractDoctrineRepository implements PrCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtProcurePr";

    const LOCAL_ENTITY_NAME = "\Application\Entity\NmtProcurePrRow";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::deleteRow()
     */
    public function removeRow(GenericPR $rootEntity, PRRow $localEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var NmtProcurePr $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var NmtProcurePrRow $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localEntity->getId()));
        }

        if ($rowEntityDoctrine->getPr() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getPr()->getId() != $rootEntity->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getPr()->getId(), $rootEntity->getId()));
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
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericPR $rootEntity, PRRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var PRRowSnapshot $localSnapshot ;
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
        // $localSnapshot->docVersion = $rowEntityDoctrine->getDocVersion();
        $localSnapshot->revisionNo = $rowEntityDoctrine->getRevisionNo();

        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false)
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
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::store()
     */
    public function store(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false)
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

        // it is time to flush.
        $this->getDoctrineEM()->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->docVersion = $rootEntityDoctrine->getRevisionNo();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getPrAutoNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface::post()
     */
    public function post(GenericPR $rootEntity, $generateSysNumber = True)
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
        // $rootSnapshot->docVersion = $rootEntityDoctrine->getDocVersion();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getPrAutoNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    /**
     *
     * @param PRSnapshot $rootSnapshot
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtProcurePr
     */
    private function _storeHeader(PRSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
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
        $entity = PrMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        if ($generateSysNumber) {
            $entity->setPrAutoNumber($this->generateSysNumber($entity));
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
     * @param Object $rootEntityDoctrine
     * @param PRRowSnapshot $localSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param int $n
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtProcurePrRow
     */
    private function _storeRow($rootEntityDoctrine, PRRowSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof NmtProcurePr) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePrRow $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            if ($rowEntityDoctrine->getPr() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            if (! $rowEntityDoctrine->getPr()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setPr($rootEntityDoctrine);
        }

        $rowEntityDoctrine = PrMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        if ($n > 0) {
            $rowEntityDoctrine->setRowIdentifer(sprintf("%s-%s", $rootEntityDoctrine->getPrAutoNumber(), $n));
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
     * @param GenericPR $rootEntity
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseRequest\PRSnapshot
     */
    private function _getRootSnapshot(GenericPR $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var PRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param PRRow $localEntity
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    private function _getLocalSnapshot(PRRow $localEntity)
    {
        if (! $localEntity instanceof PRRow) {
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
}