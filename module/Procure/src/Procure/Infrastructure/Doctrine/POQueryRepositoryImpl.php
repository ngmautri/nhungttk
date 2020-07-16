<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\SQL\PoSQL;
use Procure\Infrastructure\Mapper\PoMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POQueryRepositoryImpl extends AbstractDoctrineRepository implements POQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getHeaderByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $sql = "
SELECT
nmt_procure_po_row.po_id AS poId
FROM nmt_procure_po_row
WHERE id = %s";

        $sql = sprintf($sql, $id);

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("poId", "poId");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getSingleResult()["poId"];
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePo $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePo')->findOneBy($criteria);
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
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePo $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePo')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getHeaderDTO()
     */
    public function getHeaderDTO($id, $token = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getHeaderById()
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

        $po = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePo')->findOneBy($criteria);
        $poDetailsSnapshot = PoMapper::createSnapshot($this->getDoctrineEM(), $po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        return PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
    }

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface::getPODetailsById()
     */
    public function getPODetailsById($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $po = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcurePo')
            ->findOneBy($criteria);

        $poDetailsSnapshot = PoMapper::createSnapshot($this->getDoctrineEM(), $po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        $rows = $this->getPoRowsDetails($id);

        // $rows = null;

        if (count($rows) == 0) {
            $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $docRowsArray = array();
        $rowIdArray = array();
        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $discountAmount = 0;
        $billedAmount = 0;
        $completedRows = 0;
        $grIdArray = array();
        $apIdArray = array();

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $poRowEntity ;*/
            $po_row = $r[0];

            $poRowDetailSnapshot = PoMapper::createRowSnapshot($this->getDoctrineEM(), $po_row);
            if ($poRowDetailSnapshot == null) {
                continue;
            }

            /**
             *
             * @todo
             */
            if ($r['confirmed_ap_balance'] <= 0 and $r['confirmed_gr_balance'] <= 0) {
                $poRowDetailSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $poRowDetailSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
            }

            if ($r["gr_id"] > 0) {
                $grIdArray[] = $r["gr_id"];
            }

            if ($r["ap_id"] > 0) {
                $apIdArray[] = $r["ap_id"];
            }

            $poRowDetailSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $poRowDetailSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $poRowDetailSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $poRowDetailSnapshot->openGrBalance = $r["open_gr_qty"];
            $poRowDetailSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $poRowDetailSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $poRowDetailSnapshot->openAPQuantity = $r["open_ap_qty"];

            $poRowDetailSnapshot->billedAmount = $r["billed_amount"];
            $poRowDetailSnapshot->openAPAmount = $poRowDetailSnapshot->netAmount - $poRowDetailSnapshot->billedAmount;

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $poRowDetailSnapshot->netAmount;
            $taxAmount = $taxAmount + $poRowDetailSnapshot->taxAmount;
            $grossAmount = $grossAmount + $poRowDetailSnapshot->grossAmount;
            $billedAmount = $billedAmount + $poRowDetailSnapshot->billedAmount;

            $poRow = PORow::makeFromSnapshot($poRowDetailSnapshot);
            $docRowsArray[] = $poRow;
            $rowIdArray[] = $poRow->getId();

            // break;
        }

        if ($completed == true) {
            $poDetailsSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $poDetailsSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;
        }

        $poDetailsSnapshot->totalRows = $totalRows;
        $poDetailsSnapshot->totalActiveRows = $totalActiveRows;
        $poDetailsSnapshot->netAmount = $netAmount;
        $poDetailsSnapshot->taxAmount = $taxAmount;
        $poDetailsSnapshot->grossAmount = $grossAmount;
        $poDetailsSnapshot->discountAmount = $discountAmount;
        $poDetailsSnapshot->billedAmount = $billedAmount;
        $poDetailsSnapshot->completedRows = $completedRows;

        $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        $rootEntity->addGrArray($grIdArray);
        $rootEntity->addApArray($apIdArray);

        return $rootEntity;
    }

    // +++++++++++++++++++++++++++++++++++++++++

    /**
     *
     * @param int $id
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function getPoRowsDetails($id)
    {
        $sql = "
SELECT
*
FROM nmt_procure_po_row
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
            
LEFT JOIN
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1 order by row_number";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $tmp1 = sprintf(" AND nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1", $id);
        $sql1 = sprintf(PoSQL::SQL_ROW_PO_AP, $tmp1);
        $sql2 = sprintf(PoSQL::SQL_ROW_PO_GR, $tmp1);

        $sql = sprintf($sql, $sql1, $sql2, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("draft_gr_qty", "draft_gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            $rsm->addScalarResult("confirmed_gr_balance", "confirmed_gr_balance");
            $rsm->addScalarResult("open_gr_qty", "open_gr_qty");
            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $rsm->addScalarResult("gr_id", "gr_id");
            $rsm->addScalarResult("ap_id", "ap_id");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getOpenItems($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $po = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcurePo')
            ->findOneBy($criteria);

        $poDetailsSnapshot = PoMapper::createSnapshot($this->getDoctrineEM(), $po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        $rows = $this->getPoRowsDetails($id);

        // $rows = null;

        if (count($rows) == 0) {
            $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
            return $rootEntity;
        }

        $docRowsArray = array();
        $rowIdArray = array();

        $totalOpenRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $billedAmount = 0;
        $completedRows = 0;
        $grIdArray = array();
        $apIdArray = array();

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $poRowEntity ;*/
            $po_row = $r[0];

            $poRowDetailSnapshot = PoMapper::createRowSnapshot($this->getDoctrineEM(), $po_row);
            if ($poRowDetailSnapshot == null) {
                continue;
            }

            /**
             *
             * @todo
             */
            if ($r['confirmed_ap_balance'] <= 0 and $r['confirmed_gr_balance'] <= 0) {
                $completedRows ++;
                continue;
            }

            $poRowDetailSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;

            if ($r["gr_id"] > 0) {
                $grIdArray[] = $r["gr_id"];
            }

            if ($r["ap_id"] > 0) {
                $apIdArray[] = $r["ap_id"];
            }

            $poRowDetailSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $poRowDetailSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $poRowDetailSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $poRowDetailSnapshot->openGrBalance = $r["open_gr_qty"];
            $poRowDetailSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $poRowDetailSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $poRowDetailSnapshot->openAPQuantity = $r["open_ap_qty"];

            $poRowDetailSnapshot->billedAmount = $r["billed_amount"];
            $poRowDetailSnapshot->openAPAmount = $poRowDetailSnapshot->netAmount - $poRowDetailSnapshot->billedAmount;

            $totalOpenRows ++;
            $netAmount = $netAmount + $poRowDetailSnapshot->netAmount;
            $taxAmount = $taxAmount + $poRowDetailSnapshot->taxAmount;
            $grossAmount = $grossAmount + $poRowDetailSnapshot->grossAmount;
            $billedAmount = $billedAmount + $poRowDetailSnapshot->billedAmount;

            $poRow = PORow::makeFromSnapshot($poRowDetailSnapshot);
            $docRowsArray[] = $poRow;
            $rowIdArray[] = $poRow->getId();

            // break;
        }

        $poDetailsSnapshot->completedRows = $completedRows;

        $rootEntity = PODoc::makeFromDetailsSnapshot($poDetailsSnapshot);
        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        $rootEntity->addGrArray($grIdArray);
        $rootEntity->addApArray($apIdArray);

        return $rootEntity;
    }
}
