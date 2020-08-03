<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Inventory\Domain\Transaction\Repository\TrxQueryRepositoryInterface;
use Inventory\Infrastructure\Mapper\TrxMapper;

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

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryMv')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = TrxMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = TrxDoc::constructFromSnapshot($rootSnapshot);
            return $rootEntity;
        }

        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
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
            // break;
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

    private function getRowsById($id)
    {
        $sql = "select
*
from nmt_inventory_trx
where 1%s";

        $tmp1 = sprintf(" AND nmt_inventory_trx.movement_id=%s AND nmt_inventory_trx.is_active=1", $id);

        $sql = sprintf($sql, $tmp1);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
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
