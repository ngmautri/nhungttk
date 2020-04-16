<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\SQL\GrSQL;
use Procure\Infrastructure\Mapper\GrMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRQueryRepositoryImpl extends AbstractDoctrineRepository implements GrQueryRepositoryInterface
{

    public function getHeaderIdByRowId($id)
    {
        $sql = "
SELECT
nmt_procure_gr_row.gr_id AS grId
FROM nmt_procure_gr_row
WHERE id = %s";

        $sql = sprintf($sql, $id);

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');
            $rsm->addScalarResult("grId", "grId");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getSingleResult()["grId"];
        } catch (NoResultException $e) {
            return null;
        }
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

        $rootSnapshot = GrMapper::createDetailSnapshot($rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = GRDoc::makeFromSnapshot($rootSnapshot);
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
        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcureGrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = GrMapper::createRowDetailSnapshot($localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
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

        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;
        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;
        $rootSnapshot->billedAmount = $billedAmount;
        $rootSnapshot->completedRows = $completedRows;

        $rootEntity = GRDoc::makeFromSnapshot($rootSnapshot);
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

        return GRDoc::makeFromSnapshot($snapshot);
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

    public function getHeaderDTO($id, $token = null)
    {}

    public function getPODetailsById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

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
