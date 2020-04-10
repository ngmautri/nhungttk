<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\GoodsReceipt\GRRow;
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericGR $rootEntity, GRRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GR is empty");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->doctrineEM->find("\Application\Entity\NmtProcureGr", $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("PO Entity not retrieved.");
        }

        if ($localEntity == null) {
            throw new InvalidArgumentException("GR row is empty");
        }

        // create snapshot
        $snapshot = $localEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("GR row snapshot can not be created");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGrRow $entity ;
         */
        if ($localEntity->getId() > 0) {

            $entity = $this->doctrineEM->find("\Application\Entity\NmtProcureGrRow", $localEntity->getId());

            if ($entity == null) {
                throw new InvalidArgumentException("Local Entity can't be retrieved.");
            }

            if ($entity->getGr() == null) {
                throw new InvalidArgumentException("Root entity is not valid");
            }

            if (! $entity->getGr()->getId() == $rootEntity->getId()) {
                throw new InvalidArgumentException("PO row is corrupted");
            }
        } else {

            $entity = new \Application\Entity\NmtProcureGrRow();
            $entity->setPo($rootEntityDoctrine);
        }

        $entity = GrMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        $this->doctrineEM->persist($entity);

        $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
        $this->doctrineEM->persist($rootEntityDoctrine);
        $this->doctrineEM->flush();

        if ($snapshot->getId() == null) {
            $snapshot->id = $entity->getId();
        }
        return $snapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity is empty");
        }
        /**
         *
         * @var GRSnapshot $snapshot ;
         */
        $snapshot = $rootEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("Snapshot not created!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $entity ;
         */

        if ($rootEntity->getId() > 0) {

            $entity = $this->getDoctrineEM()->find("\Application\Entity\NmtProcureGr", $rootEntity->getId());
            if ($entity == null) {
                throw new InvalidArgumentException("Entity not found.");
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $entity = new \Application\Entity\NmtProcureGr();
        }

        $entity = GrMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        // Optimistic Locking
        if ($rootEntity->getId() > 0) {
            $entity->setRevisionNo($entity->getRevisionNo() + 1);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        // Update ID, Revision, Doc Version Numbner
        $snapshot->id = $entity->getId();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docVersion = $entity->getDocVersion();
        return $snapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::store()
     */
    public function store(GenericGR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        try {

            if ($rootEntity == null) {
                throw new InvalidArgumentException("GenericGR not retrieved.");
            }

            $snapShot = $this->storeHeader($rootEntity, $generateSysNumber, $isPosting);

           /*  $rows = $rootEntity->getDocRows();

            if (count($rows) == null) {
                return;
            }

            foreach ($rows as $row) {
                $this->storeRow($rootEntity, $row, $isPosting);
            } */

            return $snapShot;
        } catch (\Exception $e) {
            throw new GrCreateException($e->getMessage());
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface::post()
     */
    public function post(GenericGR $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericPO not retrieved.");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRSnapshot $snapshot ;
         */
        $snapshot = $rootEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("Root Snapshot not created!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $entity ;
         */
        $entity = $this->doctrineEM->find("\Application\Entity\NmtProcureGr", $rootEntity->getId());

        if ($entity == null) {
            throw new InvalidArgumentException("PO Entity not retrieved.");
        }

        $entity = GrMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        $rows = $rootEntity->getDocRows();
        $n = 0;
        foreach ($rows as $row) {

            /** @var GRRow $row ; */

            $rowSnapshot = $row->makeSnapshot();
            if ($rowSnapshot == null) {
                continue;
            }

            /** @var \Application\Entity\NmtProcureGrRow $rowEntity ; */
            $rowEntity = $this->doctrineEM->find("\Application\Entity\NmtProcureGrRow", $row->getId());

            if ($rowEntity == null) {
                continue;
            }

            $rowEntity = GrMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $row->makeSnapshot(), $rowEntity);

            $n ++;
            $rowEntity->setRowIdentifer($entity->getSysNumber() . '-' . $n);

            $this->doctrineEM->persist($rowEntity);
        }

        $entity->setRevisionNo($entity->getRevisionNo() + 1);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        // Update ID, Revision, Doc Version Numbner
        $snapshot->id = $entity->getId();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docVersion = $entity->getDocVersion();
        return $snapshot;
    }
}
