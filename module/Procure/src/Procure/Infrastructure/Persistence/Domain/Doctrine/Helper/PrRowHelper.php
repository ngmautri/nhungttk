<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\SQL\PrRowSQL;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PrRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getRowsByPrId(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }

        $tmp1 = '';
        $tmp2 = '';

        if ($filter->getPrId() > 0) {
            $tmp1 .= sprintf(" AND nmt_procure_pr_row.pr_id=%s", $filter->getPrId());
            $tmp2 .= sprintf(" AND nmt_procure_pr.id=%s", $filter->getPrId());
        }

        $sql = self::createSQL($tmp1, $tmp2);
        $sql . self::createSortBy($filter) . self::createLimitOffset($filter);

        return self::exeQuery($doctrineEM, $sql);
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PrRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getRows(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }

        $sql = self::createPrRowsSQL($filter);
        $sql = $sql . self::createSortBy($filter) . self::createLimitOffset($filter);

        return self::exeQuery($doctrineEM, $sql);
    }

    public static function getTotalRows(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }

        $sql = self::createPrRowsSQL($filter);
        $f = "select count(*) as total_row from (%s) as t ";
        $sql = sprintf($f, $sql);
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $rsm->addScalarResult("total_row", "total_row");
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * |=============================
     * | Exe Query
     * |
     * |=============================
     */
    /**
     * Helper
     *
     * @param PrRowReportSqlFilter $filter
     * @return string
     */
    public static function createPrRowsSQL(PrRowReportSqlFilter $filter, $includeLastAP = true)
    {
        $tmp1 = '';

        if ($filter->getIsActive() == 1) {
            $tmp1 = $tmp1 . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($filter->getIsActive() == - 1) {
            $tmp1 = $tmp1 . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($filter->getDocYear() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND year(nmt_procure_pr.created_on) =%s", $filter->getDocYear());
        }

        if ($filter->getItemId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_inventory_item.id =%s", $filter->getItemId());
        }

        if ($filter->getPrId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_procure_pr_row.pr_id =%s", $filter->getPrId());
        }

        $tmp2 = '';

        if ($filter->getDocYear() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND year(nmt_procure_pr.created_on) =%s", $filter->getDocYear());
        }

        if ($filter->getItemId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_inventory_item.id  =%s", $filter->getItemId());
        }

        if ($filter->getBalance() == 0) {
            $tmp2 = $tmp2 . " HAVING nmt_procure_pr_row.converted_standard_quantity <=  posted_standard_gr_qty";
        } elseif ($filter->getBalance() == 1) {
            $tmp2 = $tmp2 . " HAVING nmt_procure_pr_row.converted_standard_quantity >  posted_standard_gr_qty";
        }

        if ($filter->getPrId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_procure_pr_row.pr_id =%s", $filter->getPrId());
        }

        if ($includeLastAP) {
            return self::createSQL($tmp1, $tmp2);
        }

        return self::createSQL1($tmp1, $tmp2);
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param array $rows
     * @return Generator
     */
    public static function createRowsGenerator(EntityManager $doctrineEM, $rows)
    {
        if ($rows == null) {
            yield null;
        }

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = PrMapper::createRowSnapshot($doctrineEM, $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            $localSnapshot->qoQuantity = $r["qo_qty"];
            $localSnapshot->standardQoQuantity = $r["standard_qo_qty"];
            $localSnapshot->postedQoQuantity = $r["posted_qo_qty"];
            $localSnapshot->postedStandardQoQuantity = $r["posted_standard_qo_qty"];

            $localSnapshot->draftPoQuantity = $r["po_qty"];
            $localSnapshot->standardPoQuantity = $r["standard_po_qty"];
            $localSnapshot->postedPoQuantity = $r["posted_po_qty"];
            $localSnapshot->postedStandardPoQuantity = $r["posted_standard_po_qty"];

            $localSnapshot->draftGrQuantity = $r["gr_qty"];
            $localSnapshot->standardGrQuantity = $r["standard_gr_qty"];
            $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $localSnapshot->postedStandardGrQuantity = $r["posted_standard_gr_qty"];

            $localSnapshot->draftApQuantity = $r["ap_qty"];
            $localSnapshot->standardApQuantity = $r["standard_ap_qty"];
            $localSnapshot->postedApQuantity = $r["posted_ap_qty"];
            $localSnapshot->postedStandardApQuantity = $r["posted_standard_ap_qty"];

            $localSnapshot->draftStockQrQuantity = $r["stock_gr_qty"];
            $localSnapshot->standardStockQrQuantity = $r["standard_stock_gr_qty"];
            $localSnapshot->postedStockQrQuantity = $r["posted_stock_gr_qty"];
            $localSnapshot->postedStandardStockQrQuantity = $r["posted_standard_stock_gr_qty"];

            $localSnapshot->setLastVendorName($r["last_vendor_name"]);
            $localSnapshot->setLastUnitPrice($r["last_unit_price"]);
            $localSnapshot->setLastStandardUnitPrice($r["last_standard_unit_price"]);
            $localSnapshot->setLastStandardConvertFactor($r["last_standard_convert_factor"]);
            $localSnapshot->setLastCurrency($r["last_currency_iso3"]);

            $localEntity = PRRow::constructFromDB($localSnapshot);

            yield $localEntity;
        }
    }

    /*
     * |=============================
     * | Exe Query
     * |
     * |=============================
     */
    private static function exeQuery(EntityManager $doctrineEM, $sql)
    {
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');

            $rsm->addScalarResult("pr_qty", "pr_qty");

            $rsm->addScalarResult("qo_qty", "qo_qty");
            $rsm->addScalarResult("posted_qo_qty", "posted_qo_qty");
            $rsm->addScalarResult("standard_qo_qty", "standard_qo_qty");
            $rsm->addScalarResult("posted_standard_qo_qty", "posted_standard_qo_qty");

            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");
            $rsm->addScalarResult("standard_po_qty", "standard_po_qty");
            $rsm->addScalarResult("posted_standard_po_qty", "posted_standard_po_qty");

            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            $rsm->addScalarResult("standard_gr_qty", "standard_gr_qty");
            $rsm->addScalarResult("posted_standard_gr_qty", "posted_standard_gr_qty");

            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");
            $rsm->addScalarResult("standard_stock_gr_qty", "standard_stock_gr_qty");
            $rsm->addScalarResult("posted_standard_stock_gr_qty", "posted_standard_stock_gr_qty");

            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("standard_ap_qty", "standard_ap_qty");
            $rsm->addScalarResult("posted_standard_ap_qty", "posted_standard_ap_qty");

            $rsm->addScalarResult("pr_name", "pr_name");
            $rsm->addScalarResult("pr_year", "pr_year");

            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("last_vendor_name", "last_vendor_name");
            $rsm->addScalarResult("last_unit_price", "last_unit_price");
            $rsm->addScalarResult("last_standard_unit_price", "last_standard_unit_price");
            $rsm->addScalarResult("last_standard_convert_factor", "last_standard_convert_factor");
            $rsm->addScalarResult("last_currency_iso3", "last_currency_iso3");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private static function exeCountQuery(EntityManager $doctrineEM, $sql)
    {
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * |=============================
     * | Create sql parts.
     * |
     * |=============================
     */
    private static function createSQL($tmp1, $tmp2)
    {
        $sql = PrRowSQL::PR_ROW_SQL;
        $sql1 = sprintf(PrRowSQL::PR_QO_SQL, $tmp1);
        $sql2 = sprintf(PrRowSQL::PR_PO_SQL, $tmp1);
        $sql3 = sprintf(PrRowSQL::PR_POGR_SQL, $tmp1);
        $sql4 = sprintf(PrRowSQL::PR_STOCK_GR_SQL, $tmp1);
        $sql5 = sprintf(PrRowSQL::PR_AP_SQL, $tmp1);
        $sql6 = sprintf(PrRowSQL::ITEM_LAST_AP_SQL, $tmp1);
        return sprintf($sql, $sql1, $sql2, $sql3, $sql4, $sql5, $sql6, $tmp2);
    }

    private static function createSQL1($tmp1, $tmp2)
    {
        $sql = PrRowSQL::PR_ROW_SQL_1;
        $sql1 = sprintf(PrRowSQL::PR_QO_SQL, $tmp1);
        $sql2 = sprintf(PrRowSQL::PR_PO_SQL, $tmp1);
        $sql3 = sprintf(PrRowSQL::PR_POGR_SQL, $tmp1);
        $sql4 = sprintf(PrRowSQL::PR_STOCK_GR_SQL, $tmp1);
        $sql5 = sprintf(PrRowSQL::PR_AP_SQL, $tmp1);
        return sprintf($sql, $sql1, $sql2, $sql3, $sql4, $sql5, $tmp2);
    }

    private static function createCountTotalSQL($tmp1, $tmp2)
    {
        $sql = PrRowSQL::PR_ROW_SQL;
        $sql1 = "";
        $sql2 = sprintf(PrRowSQL::PR_PO_SQL, $tmp1);
        $sql3 = sprintf(PrRowSQL::PR_POGR_SQL, $tmp1);
        $sql4 = sprintf(PrRowSQL::PR_STOCK_GR_SQL, $tmp1);
        $sql5 = sprintf(PrRowSQL::PR_AP_SQL, $tmp1);
        $sql6 = "";
        return sprintf($sql, $sql1, $sql2, $sql3, $sql4, $sql5, $sql6, $tmp2);
    }

    private static function createSortBy(PrRowReportSqlFilter $filter)
    {
        $tmp = '';
        switch ($filter->getSortBy()) {
            case "itemName":
                $tmp = $tmp . " ORDER BY nmt_inventory_item.item_name " . $filter->getSort();
                break;

            case "prNumber":
                $tmp = $tmp . " ORDER BY nmt_procure_pr.pr_number " . $filter->getSort();
                break;

            case "prSubmitted":
                $tmp = $tmp . " ORDER BY nmt_procure_pr.submitted_on " . $filter->getSort();
                break;

            case "balance":
                $tmp = $tmp . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.posted_gr_qty,0) " . $filter->getSort();
                break;
        }

        return $tmp;
    }

    private static function createLimitOffset(PrRowReportSqlFilter $filter)
    {
        $tmp = '';

        if ($filter->getLimit() > 0) {
            $tmp = $tmp . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $tmp = $tmp . " OFFSET " . $filter->getOffset();
        }

        return $tmp;
    }
}
