<?php
namespace Procure\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\PoSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoReportHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PoReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
    nmt_procure_po.*,
    COUNT(nmt_procure_po_row.po_row_id) AS total_row,
    SUM(nmt_procure_po_row.net_amount) AS net_amount,
    SUM(nmt_procure_po_row.tax_amount) AS tax_amount,
    SUM(nmt_procure_po_row.gross_amount) AS gross_amount,
    SUM(nmt_procure_po_row.billed_amount) AS billed_amount,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS gr_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0))>0 AND (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0)) < nmt_procure_po_row.po_qty  THEN  1 ELSE 0 END) AS gr_partial_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS ap_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0))>0 AND (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0)) < nmt_procure_po_row.po_qty  THEN  1 ELSE 0 END) AS ap_partial_completed
    FROM nmt_procure_po
LEFT JOIN
(
%s
)
AS nmt_procure_po_row
ON nmt_procure_po_row.po_id = nmt_procure_po.id
WHERE 1
";

        $sql1 = PoSQL::PO_ROW_ALL;

        $sql = \sprintf($sql, $sql1);

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }

        if ($filter->getCurrentState() != null) {
            $sql = $sql . \sprintf(" AND nmt_procure_po.current_state ='%s'", $filter->getCurrentState());
        }

        if ($filter->getVendorId() != null) {
            $sql = $sql . \sprintf(' AND nmt_procure_po.vendor_id = %s', $filter->getVendorId());
        }

        if ($filter->getDocStatus() != null) {
            $sql = $sql . \sprintf(' AND nmt_procure_po.doc_status ="%s"', $filter->getDocStatus());
        }

        $sql = $sql . " GROUP BY nmt_procure_po.id";

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_procure_po.sys_number " . $sort;
                break;

            case "poDate":
                $sql = $sql . " ORDER BY nmt_procure_po.contract_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_po.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY nmt_procure_po.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY nmt_procure_po.currency_iso3 " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        //
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $rsm->addScalarResult("ap_completed", "ap_completed");
            $rsm->addScalarResult("gr_completed", "gr_completed");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|number
     */
    static public function getListTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PoReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
    nmt_procure_po.*,
    COUNT(nmt_procure_po_row.po_row_id) AS total_row,
    SUM(nmt_procure_po_row.po_row_id) AS billed_amount,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS gr_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0))>0 AND (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_gr_qty, 0)) < nmt_procure_po_row.po_qty  THEN  1 ELSE 0 END) AS gr_partial_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS ap_completed,
    SUM(CASE WHEN (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0))>0 AND (nmt_procure_po_row.po_qty - IFNULL(nmt_procure_po_row.posted_ap_qty, 0)) < nmt_procure_po_row.po_qty  THEN  1 ELSE 0 END) AS ap_partial_completed
    FROM nmt_procure_po
LEFT JOIN
(
%s
)
AS nmt_procure_po_row
ON nmt_procure_po_row.po_id = nmt_procure_po.id
WHERE 1
";

        $sql1 = PoSQL::PO_ROW_ALL;

        $sql = \sprintf($sql, $sql1);

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }

        if ($filter->getCurrentState() != null) {
            $sql = $sql . \sprintf(" AND nmt_procure_po.current_state ='%s'", $filter->getCurrentState());
        }

        if ($filter->getDocStatus() != null) {
            $sql = $sql . \sprintf(" AND nmt_procure_po.doc_status ='%s'", $filter->getDocStatus());
        }

        $sql = $sql . " GROUP BY nmt_procure_po.id";

        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|object
     */
    static public function getAllRow(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PoReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
nmt_procure_po_row.*,
fin_vendor_invoice_row.*,
nmt_procure_gr_row.draft_gr_qty,
nmt_procure_gr_row.posted_gr_qty,
            
nmt_procure_gr_row.confirmed_gr_balance,
nmt_procure_gr_row.open_gr_qty
            
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_po
on nmt_procure_po.id = nmt_procure_po_row.po_id
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
            
LEFT JOIN
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
            
WHERE 1 AND nmt_procure_po_row.is_active=1 AND nmt_procure_po.doc_status='posted'%s";
        /**
         *
         * @todo To add Return and Credit Memo
         */

        $sql_tmp = '';
        $sql_tmp1 = '';

        if ($filter->getDocYear() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND year(nmt_procure_po.doc_date)=%s", $filter->getDocYear());
        }

        if ($filter->getVendorId() != null) {
            $sql_tmp = $sql_tmp . \sprintf(' AND nmt_procure_po.vendor_id = %s', $filter->getVendorId());
        }

        if ($filter->getBalance() == 0) {
            $sql_tmp1 = $sql_tmp1 . " HAVING (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) <= 0";
        }

        if ($filter->getBalance() == 1) {
            $sql_tmp1 = $sql_tmp1 . " HAVING (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) > 0";
        }
        if ($filter->getBalance() == - 1) {
            $sql_tmp1 = $sql_tmp1 . " HAVING (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) < 0";
        }

        switch ($sort_by) {
            case "vendorName":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_po.vendor_name " . $sort;
                break;

            case "poNumber":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_po_row.contract_no " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp1 = $sql_tmp1 . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp1 = $sql_tmp1 . " OFFSET " . $offset;
        }

        $sql1 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_AP, $sql_tmp);
        $sql2 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_GR, $sql_tmp);

        $sql = sprintf($sql, $sql1, $sql2, $sql_tmp . $sql_tmp1);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
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

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getAllRowTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PoReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
nmt_procure_po_row.*,
fin_vendor_invoice_row.*,
nmt_procure_gr_row.draft_gr_qty,
nmt_procure_gr_row.posted_gr_qty,
            
nmt_procure_gr_row.confirmed_gr_balance,
nmt_procure_gr_row.open_gr_qty
            
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_po
on nmt_procure_po.id = nmt_procure_po_row.po_id
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
            
LEFT JOIN
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
            
WHERE 1 AND nmt_procure_po_row.is_active=1 AND nmt_procure_po.doc_status='posted'%s";
        /**
         *
         * @todo To add Return and Credit Memo
         */

        $sql_tmp = '';
        $sql_tmp1 = '';

        if ($filter->getDocYear() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND year(nmt_procure_po.doc_date)=%s", $filter->getDocYear());
        }

        if ($filter->getVendorId() != null) {
            $sql_tmp = $sql_tmp . \sprintf(' AND nmt_procure_po.vendor_id = %s', $filter->getVendorId());
        }

        if ($filter->getBalance() == 0) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) <= 0";
        }
        if ($filter->getBalance() == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) > 0";
        }
        if ($filter->getBalance() == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) < 0";
        }

        $sql1 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_AP, $sql_tmp);
        $sql2 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_GR, $sql_tmp);

        $sql = sprintf($sql, $sql1, $sql2, $sql_tmp . $sql_tmp1);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return count($resulst);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
