<?php
namespace Procure\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\PrSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrReportHelper
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

        if (! $filter instanceof PrReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
    nmt_procure_pr.*,
    COUNT(nmt_procure_pr_row.pr_row_id) AS total_row,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS gr_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0))>0 AND (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0)) < nmt_procure_pr_row.pr_qty  THEN  1 ELSE 0 END) AS gr_partial_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS ap_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0))>0 AND (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0)) < nmt_procure_pr_row.pr_qty  THEN  1 ELSE 0 END) AS ap_partial_completed
    FROM nmt_procure_pr
LEFT JOIN
(
%s
)
AS nmt_procure_pr_row
ON nmt_procure_pr_row.pr_id = nmt_procure_pr.id
WHERE 1
";

        $sql1 = PrSQL::PR_ROW_ALL;

        $sql = \sprintf($sql, $sql1);

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active = 0";
        }

        if ($filter->getDocYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_pr.submitted_on) = %s", $filter->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_pr.id";

        // fullfiled
        if ($filter->getBalance() == 0) {
            $sql = $sql . " HAVING total_row <=  gr_completed";
        } elseif ($filter->getBalance() == 1) {
            $sql = $sql . " HAVING total_row >  gr_completed";
        }

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_procure_pr.pr_auto_number " . $sort;
                break;

            case "docDate":
                $sql = $sql . " ORDER BY nmt_procure_pr.doc_date " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_pr.submitted_on " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("gr_completed", "gr_completed");
            $rsm->addScalarResult("ap_completed", "ap_completed");
            $rsm->addScalarResult("gr_partial_completed", "gr_partial_completed");
            $rsm->addScalarResult("ap_partial_completed", "ap_partial_completed");
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

        if (! $filter instanceof PrReportSqlFilter) {
            return null;
        }

        $sql = "
SELECT
    nmt_procure_pr.*,
    COUNT(nmt_procure_pr_row.pr_row_id) AS total_row,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS gr_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0))>0 AND (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_gr_qty, 0)) < nmt_procure_pr_row.pr_qty  THEN  1 ELSE 0 END) AS gr_partial_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS ap_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0))>0 AND (nmt_procure_pr_row.pr_qty - IFNULL(nmt_procure_pr_row.posted_ap_qty, 0)) < nmt_procure_pr_row.pr_qty  THEN  1 ELSE 0 END) AS ap_partial_completed
    FROM nmt_procure_pr
LEFT JOIN
(
%s
)
AS nmt_procure_pr_row
ON nmt_procure_pr_row.pr_id = nmt_procure_pr.id
WHERE 1
";

        $sql1 = PrSQL::PR_ROW_ALL;

        $sql = \sprintf($sql, $sql1);

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active = 0";
        }

        if ($filter->getDocYear() > 0) {
            $sql = $sql . sprintf(" AND year(nmt_procure_pr.submitted_on) =%s", $filter->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_pr.id";

        // fullfiled
        if ($filter->getBalance() == 0) {
            $sql = $sql . " HAVING total_row <=  gr_completed";
        } elseif ($filter->getBalance() == 1) {
            $sql = $sql . " HAVING total_row >  gr_completed";
        }
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("gr_completed", "gr_completed");
            $rsm->addScalarResult("ap_completed", "ap_completed");
            $rsm->addScalarResult("gr_partial_completed", "gr_partial_completed");
            $rsm->addScalarResult("ap_partial_completed", "ap_partial_completed");

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
        if (! $filter instanceof PrReportSqlFilter) {
            return null;
        }

        $sql = PrSQL::PR_ROW_SQL;

        $sql_tmp1 = '';
        if ($filter->getIsActive() == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($filter->getIsActive() == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($filter->getDocYear() > 0) {
            $sql_tmp1 = $sql_tmp1 . \sprintf(" AND year(nmt_procure_pr.created_on) =%s", $filter->getDocYear());
        }

        if ($filter->getItemId() > 0) {
            $sql_tmp1 = $sql_tmp1 . \sprintf(" AND nmt_inventory_item.id  =%s", $filter->getItemId());
        }

        if ($filter->getBalance() == 0) {
            $sql_tmp1 = $sql_tmp1 . " HAVING nmt_procure_pr_row.quantity <=  posted_gr_qty";
        }
        if ($filter->getBalance() == 1) {
            $sql_tmp1 = $sql_tmp1 . " HAVING nmt_procure_pr_row.quantity >  posted_gr_qty";
        }

        switch ($sort_by) {
            case "itemName":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;

            case "prNumber":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_pr.pr_number " . $sort;
                break;

            case "balance":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.posted_gr_qty,0) " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp1 = $sql_tmp1 . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp1 = $sql_tmp1 . " OFFSET " . $offset;
        }

        $sql = sprintf($sql, $sql_tmp1);
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');

            $rsm->addScalarResult("pr_qty", "pr_qty");

            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");

            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");

            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");

            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");

            $rsm->addScalarResult("pr_name", "pr_name");
            $rsm->addScalarResult("pr_year", "pr_year");

            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("last_vendor_name", "last_vendor_name");
            $rsm->addScalarResult("last_unit_price", "last_unit_price");
            $rsm->addScalarResult("currency_iso3", "currency_iso3");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getAllRowTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $filter instanceof PrReportSqlFilter) {
            return null;
        }

        $sql = PrSQL::PR_ROW_SQL;

        $sql_tmp1 = '';

        if ($filter->getIsActive() == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($filter->getIsActive() == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($filter->getDocStatus() != null) {
            $sql_tmp1 = $sql_tmp1 . \sprintf(" AND nmt_procure_pr.doc_status ='%s'", $filter->getDocStatus());
        }

        if ($filter->getDocYear() > 0) {
            $sql_tmp1 = $sql_tmp1 . \sprintf(" AND year(nmt_procure_pr.submitted_on) =%s", $filter->getDocYear());
        }

        if ($filter->getBalance() == 0) {
            $sql_tmp1 = $sql_tmp1 . " HAVING nmt_procure_pr_row.quantity <=  posted_gr_qty";
        }
        if ($filter->getBalance() == 1) {
            $sql_tmp1 = $sql_tmp1 . " HAVING nmt_procure_pr_row.quantity >  posted_gr_qty";
        }

        $sql = sprintf($sql, $sql_tmp1);
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
