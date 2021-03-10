<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Domain\Util\Translator;
use Application\Entity\NmtProcureGrRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;
use Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\Helper\GrHelper;
use Procure\Infrastructure\Doctrine\SQL\GrSQL;
use Procure\Infrastructure\Mapper\GrMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRQueryRepositoryImpl extends AbstractDoctrineRepository implements GrQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getLazyRootEntityById()
     */
    public function getLazyRootEntityById($id)
    {
        if ($id == null) {
            $f = Translator::translate("Could not create GR document. Document Id is empty");
            throw new \RuntimeException(\sprintf($f, $id));
        }

        $criteria = array(
            'id' => $id
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcureGr')
            ->findOneBy($criteria);

        $rootSnapshot = GrMapper::createDetailSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            $f = Translator::translate("Could not create GR Document with #%");
            throw new \RuntimeException(\sprintf($f, $id));
        }

        $rootEntity = GRFactory::constructFromDB($rootSnapshot);
        $rootEntity->setLazyRowSnapshotCollectionReference($this->createLazyRowSnapshotCollectionReference($rootEntity));
        return $rootEntity;
    }

    /**
     *
     * @param GenericGR $rootEntity
     * @return \Closure
     */
    private function createLazyRowSnapshotCollectionReference(GenericGR $rootEntity)
    {
        return function () use ($rootEntity) {

            // $rows = $this->getRowsById($rootEntity->getId());

            $rows = GrHelper::getRows($this->getDoctrineEM(), $rootEntity->getId(), 'warehouse_id, row_number');

            if (count($rows) == 0) {
                return null;
            }

            $collection = new ArrayCollection();

            foreach ($rows as $r) {

                $rowClosure = function () use ($r) {

                    $localEnityDoctrine = $r[0];

                    /**@var \Application\Entity\NmtInventoryTrx $entityDoctrine ;*/

                    $rowSnapshot = GrMapper::createRowDetailSnapshot($this->getDoctrineEM(), $localEnityDoctrine);
                    $rowSnapshot->draftAPQuantity = $r["draft_ap_qty"];
                    $rowSnapshot->postedAPQuantity = $r["posted_ap_qty"];
                    $rowSnapshot->openAPQuantity = $r["open_ap_qty"];
                    $rowSnapshot->billedAmount = $r["billed_amount"];

                    return $rowSnapshot;
                };

                $collection->add($rowClosure);
            }
            return $collection;
        };
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var NmtProcureGrRow $doctrineEntity
         */
        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGrRow')->findOneBy($criteria);
        if ($doctrineEntity == null) {
            return null;
        }

        if ($doctrineEntity->getGr() != null) {
            return $doctrineEntity->getGr()->getId();
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getRootEntityByTokenId()
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
            ->getRepository('\Application\Entity\NmtProcureGr')
            ->findOneBy($criteria);

        $rootSnapshot = GrMapper::createDetailSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = GRFactory::constructFromDB($rootSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $completedRows = 0;
        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $billedAmount = 0;
        $grossAmount = 0;
        $targetWhList = new ArrayCollection();
        $targetDepartmentList = new ArrayCollection();

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcureGrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = GrMapper::createRowDetailSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            if ($localSnapshot->getWarehouse() !== null) {
                $targetWhList->add($localSnapshot->getWarehouse());
            }

            if ($localSnapshot->getPrDepartmentName() !== null) {
                $targetDepartmentList->add($localSnapshot->getPrDepartmentName());
            }

            /**
             *
             * @todo
             */
            if ($r['open_ap_qty'] <= 0) {
                $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
            }

            $localSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $localSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $localSnapshot->openAPQuantity = $r["open_ap_qty"];
            $localSnapshot->billedAmount = $r["billed_amount"];
            $localSnapshot->openAPAmount = $localSnapshot->netAmount - $localSnapshot->billedAmount;

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $localSnapshot->netAmount;
            $taxAmount = $taxAmount + $localSnapshot->taxAmount;
            $grossAmount = $grossAmount + $localSnapshot->grossAmount;
            $billedAmount = $billedAmount + $localSnapshot->billedAmount;

            $localEntity = GRRow::makeFromSnapshot($localSnapshot);

            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();

            // break;
        }

        if ($completed == true) {
            $rootSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $rootSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
        }

        $rootSnapshot->targetDepartmentList = $targetDepartmentList;
        $rootSnapshot->targetWhList = $targetWhList;
        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;
        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;
        $rootSnapshot->billedAmount = $billedAmount;
        $rootSnapshot->completedRows = $completedRows;

        $rootEntity = GRFactory::constructFromDB($rootSnapshot);

        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureGr $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        if ($token == null) {
            $criteria = array(
                'id' => $id
            );
        } else {
            $criteria = array(
                'id' => $id,
                'token' => $token
            );
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        $snapshot = GrMapper::createDetailSnapshot($entity);

        if ($snapshot == null) {
            return null;
        }

        return GRFactory::constructFromDB($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureGr $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getDocVersion()
            ];
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getHeaderDTO()
     */
    public function getHeaderDTO($id, $token = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getById()
     */
    public function getById($id, $outputStragegy = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getByUUID()
     */
    public function getByUUID($uuid)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::findAll()
     */
    public function findAll()
    {}

    // +++++++++++++++++++++++++++++++++++++++++

    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getRowsById($id)
    {
        $sql = "
SELECT
*
FROM nmt_procure_gr_row

LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id = nmt_procure_gr_row.id

WHERE nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1 order by row_number";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $tmp1 = sprintf(" AND nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1", $id);
        $sql1 = sprintf(GrSQL::SQL_ROW_GR_AP, $tmp1);

        $sql = sprintf($sql, $sql1, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');
            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
