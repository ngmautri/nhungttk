<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\POCmdRepositoryInterface;
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
     * @see \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericPO $rootEntity, PORow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("PO is empty");
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
        }

        $entity = PoMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        if ($isPosting) {
            $entity->setLastChangeOn(new \DateTime());
            $entity->setDocStatus(PODocStatus::DOC_STATUS_POSTED);
            $entity->setIsDraft(0);
            $entity->setIsPosted(1);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface::post()
     */
    public function post(GenericPO $rootEntity, $generateSysNumber = True)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface::createRow()
     */
    public function createRow($poId, PORow $localEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface::storeHeader()
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
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface::store()
     */
    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}
}
