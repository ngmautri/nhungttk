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

        $sql = PoSQL::PO_LIST;

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
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");

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

        $sql = PoSQL::PO_LIST;

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
