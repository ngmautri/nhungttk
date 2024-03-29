<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface;
use Procure\Infrastructure\Mapper\ApMapper;
use InvalidArgumentException;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APCmdRepositoryImpl extends AbstractDoctrineRepository implements APCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\FinVendorInvoice";

    const LOCAL_ENTITY_NAME = "\Application\Entity\FinVendorInvoiceRow";

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface::removeRow()
     */
    public function removeRow(GenericAP $rootEntity, APRow $localEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var FinVendorInvoice $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var FinVendorInvoiceRow $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->getDoctrineEM()->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localEntity->getId()));
        }

        //
        if ($rowEntityDoctrine->getInvoice() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getInvoice()->getId() != $rootEntity->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getInvoice()->getId(), $rootEntity->getId()));
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
     * @see \Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface::storeRow()
     */
    public function storeRow(GenericAP $rootEntity, APRow $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new \InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new \RuntimeException("Doctrine root entity not found.");
        }

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeRow($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
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
     * @see \Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface::storeHeader()
     */
    public function storeHeader(GenericAP $rootEntity, $generateSysNumber = false, $isPosting = false)
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
    public function store(GenericAP $rootEntity, $generateSysNumber = false, $isPosting = false)
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
     * @see \Procure\Domain\AccountPayable\Repository\APCmdRepositoryInterface::post()
     */
    public function post(GenericAP $rootEntity, $generateSysNumber = True)
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
     * @param \Procure\Domain\AccountPayable\APSnapshot $rootSnapshot
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @return \Application\Entity\FinVendorInvoice
     */
    private function _storeHeader(APSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $entity ;
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
        $entity = ApMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

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
     * @param \Procure\Domain\AccountPayable\APRowSnapshot $localSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param boolean $n
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    private function _storeRow($rootEntityDoctrine, APRowSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof FinVendorInvoice) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\FinVendorInvoiceRow $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getInvoice() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getInvoice()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setInvoice($rootEntityDoctrine);
        }

        $rowEntityDoctrine = ApMapper::mapRowSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

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
     * @param \Procure\Domain\AccountPayable\GenericAP $rootEntity
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APSnapshot
     */
    private function _getRootSnapshot(GenericAP $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param \Procure\Domain\AccountPayable\APRow $localEntity
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    private function _getLocalSnapshot(APRow $localEntity)
    {
        if (! $localEntity instanceof APRow) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        /**
         *
         * @todo
         * @var APRowSnapshot $localSnapshot ;
         */
        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }
}