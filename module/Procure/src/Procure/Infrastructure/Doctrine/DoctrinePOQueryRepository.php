<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\POQueryRepositoryInterface;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Infrastructure\Mapper\PoMapper;
use Procure\Domain\PurchaseOrder\PODoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePOQueryRepository extends AbstractDoctrineRepository implements POQueryRepositoryInterface
{

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\POQueryRepositoryInterface::getPODetailsById()
     */
    public function getPODetailsById($id)
    {

        $po = $this->getDoctrineEM()->getRepository('Application\Entity\NmtProcurePo')->find($id);
        $poDetailsSnapshot = PoMapper::createDetailSnapshot($po);

        if ($poDetailsSnapshot == null) {
            return null;
        }

        $rows = $this->getPoRowsDetails($id);

        if (count($rows) == 0) {
            $rootEntity = new PODoc();
            $rootEntity->makeFromDetailsSnapshot($poDetailsSnapshot);
            return $rootEntity;
        }

        $completed = True;
        $docRowsArray = array();
        $totalRows = 0;
        $totalActiveRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $discountAmount = 0;
        $billedAmount = 0;
        $completedRows = 0;

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $poRowEntity ;*/
            $po_row = $r[0];


            $poRowDetailSnapshot = PoMapper::createRowDetailSnapshot($po_row);

            if ($poRowDetailSnapshot == null) {
                continue;
            }

            if ($r['open_gr_qty'] == 0 and $r['open_ap_qty'] == 0) {
                $poRowDetailSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_COMPLETED;
                $completedRows ++;
            } else {
                $completed = false;
                $poRowDetailSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED;
            }

            $poRowDetailSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $poRowDetailSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $poRowDetailSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $poRowDetailSnapshot->openGrBalance = $r["open_gr_qty"];
            $poRowDetailSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $poRowDetailSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $poRowDetailSnapshot->openAPQuantity = $r["open_ap_qty"];
            $poRowDetailSnapshot->billedAmount = $r["billed_amount"];

            $totalRows ++;
            $totalActiveRows ++;
            $netAmount = $netAmount + $poRowDetailSnapshot->netAmount;
            $taxAmount = $taxAmount + $poRowDetailSnapshot->taxAmount;
            $grossAmount = $grossAmount + $poRowDetailSnapshot->grossAmount;
            $billedAmount = $billedAmount + $poRowDetailSnapshot->billedAmount;

            $poRow = new PORow();
            $poRow->makeFromDetailsSnapshot($poRowDetailSnapshot);
            $docRowsArray[] = $poRow;
        }

        if ($completed == true) {
            $poDetailsSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_COMPLETED;
        } else {
            $poDetailsSnapshot->transactionStatus = \Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED;
        }

        $poDetailsSnapshot->totalRows = $totalRows;
        $poDetailsSnapshot->totalActiveRows = $totalActiveRows;
        $poDetailsSnapshot->netAmount = $netAmount;
        $poDetailsSnapshot->taxAmount = $taxAmount;
        $poDetailsSnapshot->grossAmount = $grossAmount;
        $poDetailsSnapshot->discountAmount = $discountAmount;
        $poDetailsSnapshot->billedAmount = $billedAmount;
        $poDetailsSnapshot->completedRows = $completedRows;

        $rootEntity = new PODoc();
        $rootEntity->makeFromDetailsSnapshot($poDetailsSnapshot);

        $rootEntity->setDocRows($docRowsArray);
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
        $sql1 = "
SELECT
    nmt_procure_po_row.id AS po_row_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount
            
FROM nmt_procure_po_row
            
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id =  nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

        $sql2 = "
SELECT
            
	IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS draft_gr_qty,
    IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS posted_gr_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_gr_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END)-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS open_gr_qty,
    nmt_procure_po_row.id as po_row_id
            
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id =  nmt_procure_po_row.id
            
WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

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

        $sql1 = sprintf($sql1, $id);
        $sql2 = sprintf($sql2, $id);

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

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
