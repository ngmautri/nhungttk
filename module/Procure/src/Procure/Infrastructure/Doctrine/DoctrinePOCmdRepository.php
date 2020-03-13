<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\Exception\InvalidArgumentException;
use Ramsey;
use Procure\Infrastructure\Mapper\PoMapper;
use Procure\Domain\PurchaseOrder\PODocStatus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePOCmdRepository extends AbstractDoctrineRepository implements POCmdRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericPO $rootEntity, PORow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("PO is empty");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePo $entity ;
         */
        $rootEntityDoctrine = $this->doctrineEM->find("\Application\Entity\NmtProcurePo", $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("PO Entity not retrieved.");
        }

        if ($localEntity == null) {
            throw new InvalidArgumentException("PO row is empty");
        }

        // create snapshot
        $snapshot = $localEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("PO row snapshot can not be created");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePoRow $entity ;
         */
        if ($localEntity->getId() > 0) {
            $entity = $this->doctrineEM->find("\Application\Entity\NmtProcurePoRow", $localEntity->getId());

            if ($entity == null) {
                throw new InvalidArgumentException("Transaction row can't be retrieved.");
            }

            if ($entity->getPo() == null) {
                throw new InvalidArgumentException("Root entity is not valid");
            }

            if (! $entity->getPo()->getId() == $rootEntity->getId()) {
                throw new InvalidArgumentException("PO row is corrupted");
            }

            $entity->setLastChangeOn(new \Datetime());
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {

            $entity = new \Application\Entity\NmtProcurePoRow();
            // $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
            $entity->setCreatedOn(new \Datetime());
        }

        $entity->setPo($rootEntityDoctrine);
        $entity = PoMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(PODocStatus::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        //echo " Test" . $entity->getId();
        return $entity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::post()
     */
    public function post(GenericPO $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericPO not retrieved.");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePo $entity ;
         */
        $entity = $this->doctrineEM->find("\Application\Entity\NmtProcurePo", $rootEntity->getId());

        if ($entity == null) {
            throw new InvalidArgumentException("PO Entity not retrieved.");
        }

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        $entity->setLastChangeOn(new \DateTime());
        $entity->setDocStatus(\Application\Domain\Shared\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);

        $rows = $rootEntity->getDocRows();
        $n = 0;
        foreach ($rows as $row) {

            /** @var PORow $row ; */

            /** @var \Application\Entity\NmtProcurePoRow $r ; */
            $r = $this->doctrineEM->find("\Application\Entity\NmtProcurePoRow", $row->getId());

            if ($r == null) {
                continue;
            }

            $n ++;

            // update transaction row
            $r->setDocStatus($entity->getDocStatus());
            $r->setDocType($entity->getMovementType());
            $r->setTransactionType($entity->getMovementType());
            $r->setCogsLocal($row->getCogsLocal());
            $r->setSysNumber($entity->getSysNumber() . '-' . $n);
            $this->doctrineEM->persist($r);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::createRow()
     */
    public function createRow($poId, PORow $localEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity is empty");
        }

        // create snapshot
        $snapshot = $rootEntity->makeSnapshot();
        if ($snapshot == null) {
            throw new InvalidArgumentException("Snapshot not created!");
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePo $entity ;
         */

        if ($rootEntity->getId() > 0) {

            $entity = $this->getDoctrineEM()->find("\Application\Entity\NmtProcurePo", $rootEntity->getId());
            if ($entity == null) {
                throw new InvalidArgumentException("Entity not found.");
            }

            $entity->setLastChangeOn(new \DateTime());
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $entity = new \Application\Entity\NmtProcurePo();
            $entity->setCreatedOn(new \DateTime());
        }

        $entity = PoMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(PODocStatus::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity->getId();
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface::store()
     */
    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}
}
