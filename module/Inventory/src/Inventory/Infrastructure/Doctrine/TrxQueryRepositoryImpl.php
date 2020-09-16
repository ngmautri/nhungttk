<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface;
use Inventory\Infrastructure\Doctrine\Helper\TrxHelper;
use Inventory\Infrastructure\Mapper\TrxMapper;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxQueryRepositoryImpl extends AbstractDoctrineRepository implements TrxQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryMv')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryMv')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getDocVersion()
            ];
        }

        return null;
    }

    public function getHeaderById($id, $token = null)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryMv')->findOneBy($criteria);
        $snapshot = TrxMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return TrxDoc::constructFromSnapshot($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $result = TrxHelper::getHeaderByTokenId($this->getDoctrineEM(), $id, $token);

        if ($result == null) {
            return null;
        }

        $rootEntityDoctrine = $result[0];

        $rootSnapshot = TrxMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = TrxHelper::getRowsById($this->getDoctrineEM(), $id);

        if (count($rows) == 0) {
            $rootEntity = TrxDoc::constructFromSnapshot($rootSnapshot);
            return $rootEntity;
        }

        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $docRowsArray = null;
        $rowIdArray = null;

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtInventoryTrx $localEnityDoctrine ;*/
            $localEnityDoctrine = $r;

            $localSnapshot = TrxMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }
            $netAmount = $netAmount + $localSnapshot->getNetAmount();
            $taxAmount = $taxAmount + $localSnapshot->getTaxAmount();
            $grossAmount = $grossAmount + $localSnapshot->getGrossAmount();
            $totalRows ++;
            $localEntity = TrxRow::makeFromSnapshot($localSnapshot);
            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();
        }

        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;

        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;

        $rootEntity = TransactionFactory::contructFromDB($rootSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface::getLazyRootEntityByTokenId()
     */
    public function getLazyRootEntityByTokenId($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $result = TrxHelper::getHeaderByTokenId($this->getDoctrineEM(), $id, $token);

        if ($result == null) {
            return null;
        }

        $rootEntityDoctrine = $result[0];

        $rootSnapshot = TrxMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootSnapshot->totalRows = $result['total_active_row'];
        $rootSnapshot->netAmount = $result['total_net_amount'];
        $rootSnapshot->grossAmount = $result['total_gross_amount'];

        $rootEntity = TransactionFactory::contructFromDB($rootSnapshot);
        $rootEntity->setRowsCollectionReference($this->createRowsCollectionReference($rootEntity));
        $rootEntity->setLazyRowSnapshotCollectionReference($this->createLazyRowSnapshotCollectionReference($rootEntity));
        return $rootEntity;
    }

    /**
     *
     * @param GenericTrx $rootEntity
     * @return null|Closure
     */
    private function createRowsCollectionReference(GenericTrx $rootEntity)
    {
        return function () use ($rootEntity) {

            $rows = TrxHelper::getRowsById($this->getDoctrineEM(), $rootEntity->getId());

            if (count($rows) == 0) {
                return null;
            }

            $collection = new ArrayCollection();

            foreach ($rows as $r) {

                $rowClosure = function () use ($r) {
                    /**@var \Application\Entity\NmtInventoryTrx $localEnityDoctrine ;*/
                    $localEnityDoctrine = $r;
                    $localSnapshot = TrxMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);
                    if ($localSnapshot == null) {
                        return null;
                    }
                    return TrxRow::makeFromSnapshot($localSnapshot);
                };

                $collection->add($rowClosure);
            }
            return $collection;
        };
    }

    private function createLazyRowSnapshotCollectionReference(GenericTrx $rootEntity)
    {
        return function () use ($rootEntity) {

            $rows = TrxHelper::getDetailRowsById($this->getDoctrineEM(), $rootEntity->getId());

            if (count($rows) == 0) {
                return null;
            }

            $collection = new ArrayCollection();

            foreach ($rows as $r) {

                $rowClosure = function () use ($r) {

                    $entityDoctrine = $r[0];

                    /**@var \Application\Entity\NmtInventoryTrx $entityDoctrine ;*/
                    $rowSnapshot = TrxMapper::createRowSnapshot($this->getDoctrineEM(), $entityDoctrine);
                    $rowSnapshot->stockQty = $r['onhand_qty'];
                    return $rowSnapshot;
                };

                $collection->add($rowClosure);
            }
            return $collection;
        };
    }

    public function getById($id, $outputStragegy = null)
    {}

    public function getHeaderDTO($id, $token = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}
