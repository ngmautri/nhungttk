<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PoMapper;
use Procure\Infrastructure\Persistence\SQL\PoRowSQLV1;
use Procure\Infrastructure\Persistence\SQL\Filter\PoRowReportSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PoRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getRowsByPoId(EntityManager $doctrineEM, PoRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PoRowReportSqlFilter) {
            return null;
        }

        if ($filter->getPoId() == null) {
            return null;
        }

        $sql = self::createFullSQL($filter);
        $sql . self::createSortBy($filter) . self::createLimitOffset($filter);
        $rsm = self::createFullResultMapping($doctrineEM);

        return self::runQuery($doctrineEM, $sql, $rsm);
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PoRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getRows(EntityManager $doctrineEM, PoRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PoRowReportSqlFilter) {
            return null;
        }

        // create SQL
        $sql = self::createFullSQL($filter);
        $sql = $sql . self::createSortBy($filter) . self::createLimitOffset($filter);
        $rsm = self::createFullResultMapping($doctrineEM);

        return self::runQuery($doctrineEM, $sql, $rsm);
    }

    public static function getTotalRows(EntityManager $doctrineEM, PoRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PoRowReportSqlFilter) {
            return null;
        }

        $sql = self::createSQLForTotalCount($filter);
        $f = "select count(*) as total_row from (%s) as t ";
        $sql = sprintf($f, $sql);
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
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
    private static function runQuery(EntityManager $doctrineEM, $sql, ResultSetMappingBuilder $rsm)
    {
        // echo $sql;
        try {
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * |=============================
     * | Create WHERE
     * |
     * |=============================
     */
    public static function createJoinWhere(PoRowReportSqlFilter $filter)
    {
        $tmp1 = '';

        if ($filter->getIsActive() == 1) {
            $tmp1 = $tmp1 . " AND (nmt_procure_po.is_active = 1 AND nmt_procure_po_row.is_active = 1)";
        } elseif ($filter->getIsActive() == - 1) {
            $tmp1 = $tmp1 . " AND (nmt_procure_po.is_active = 0 OR nmt_procure_po_row.is_active = 0)";
        }

        if ($filter->getDocYear() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND year(nmt_procure_po.doc_date) =%s", $filter->getDocYear());
        }

        if ($filter->getItemId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_inventory_item.id =%s", $filter->getItemId());
        }

        if ($filter->getPoId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_procure_po_row.po_id =%s", $filter->getPoId());
        }

        if ($filter->getVendorId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_procure_po_row.vendor_id =%s", $filter->getVendorId());
        }

        return $tmp1;
    }

    public static function createWhere(PoRowReportSqlFilter $filter)
    {
        $tmp2 = '';

        if ($filter->getDocYear() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND year(nmt_procure_po.doc_date) =%s", $filter->getDocYear());
        }

        if ($filter->getItemId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_inventory_item.id  =%s", $filter->getItemId());
        }

        if ($filter->getBalance() == 0) {
            $tmp2 = $tmp2 . " HAVING nmt_procure_po_row.converted_standard_quantity <=  posted_standard_gr_qty";
        } elseif ($filter->getBalance() == 1) {
            $tmp2 = $tmp2 . " HAVING nmt_procure_po_row.converted_standard_quantity >  posted_standard_gr_qty";
        }

        if ($filter->getPoId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_procure_po.id =%s", $filter->getPoId());
        }

        if ($filter->getVendorId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_procure_po.vendor_id =%s", $filter->getVendorId());
        }

        return $tmp2;
    }

    /*
     * |=============================
     * | Create Generators
     * |
     * |=============================
     */
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

            /**@var \Application\Entity\NmtProcurePoRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = PoMapper::createRowSnapshot($doctrineEM, $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            if (isset($r["gr_qty"])) {
                $localSnapshot->draftGrQuantity = $r["gr_qty"];
            }

            if (isset($r["standard_gr_qty"])) {
                $localSnapshot->standardGrQuantity = $r["standard_gr_qty"];
            }

            if (isset($r["posted_gr_qty"])) {
                $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            }

            if (isset($r["posted_standard_gr_qty"])) {
                $localSnapshot->postedStandardGrQuantity = $r["posted_standard_gr_qty"];
            }

            if (isset($r["ap_qty"])) {
                $localSnapshot->draftApQuantity = $r["ap_qty"];
            }

            if (isset($r["standard_ap_qty"])) {
                $localSnapshot->standardApQuantity = $r["standard_ap_qty"];
            }

            if (isset($r["posted_ap_qty"])) {
                $localSnapshot->postedApQuantity = $r["posted_ap_qty"];
            }

            if (isset($r["posted_standard_ap_qty"])) {
                $localSnapshot->postedStandardApQuantity = $r["posted_standard_ap_qty"];
            }

            if (isset($r["stock_gr_qty"])) {
                $localSnapshot->draftStockQrQuantity = $r["stock_gr_qty"];
            }

            if (isset($r["standard_stock_gr_qty"])) {
                $localSnapshot->standardStockQrQuantity = $r["standard_stock_gr_qty"];
            }

            if (isset($r["posted_stock_gr_qty"])) {
                $localSnapshot->postedStockQrQuantity = $r["posted_stock_gr_qty"];
            }

            if (isset($r["posted_standard_stock_gr_qty"])) {
                $localSnapshot->postedStandardStockQrQuantity = $r["posted_standard_stock_gr_qty"];
            }

            if (isset($r["billed_amount"])) {
                $localSnapshot->billedAmount = $r["billed_amount"];
            }

            $localEntity = PORow::constructFromDB($localSnapshot);

            yield $localEntity;
        }
    }

    /*
     * |=============================
     * | Mapping Result
     * |
     * |=============================
     */
    private static function mapDefaultResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
        $rsm->addScalarResult("po_qty", "po_qty");
        $rsm->addScalarResult("po_standard_qty", "po_standard_qty");
        $rsm->addScalarResult("po_number", "po_number");
        $rsm->addScalarResult("po_date", "po_date");
        $rsm->addScalarResult("po_year", "po_year");
        $rsm->addScalarResult("item_name", "item_name");
        return $rsm;
    }

    private static function mapGrResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("gr_qty", "gr_qty");
        $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
        $rsm->addScalarResult("standard_gr_qty", "standard_gr_qty");
        $rsm->addScalarResult("posted_standard_gr_qty", "posted_standard_gr_qty");
        return $rsm;
    }

    private static function mapStockGrResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
        $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");
        $rsm->addScalarResult("standard_stock_gr_qty", "standard_stock_gr_qty");
        $rsm->addScalarResult("posted_standard_stock_gr_qty", "posted_standard_stock_gr_qty");
        return $rsm;
    }

    private static function mapApResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("ap_qty", "ap_qty");
        $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
        $rsm->addScalarResult("standard_ap_qty", "standard_ap_qty");
        $rsm->addScalarResult("posted_standard_ap_qty", "posted_standard_ap_qty");
        $rsm->addScalarResult("billed_amount", "billed_amount");

        return $rsm;
    }

    /*
     * |=============================
     * | Create sql parts.
     * |
     * |=============================
     */
    public static function createFullSQL(PoRowReportSqlFilter $filter)
    {
        $sql = PoRowSQLV1::PO_ROW_SQL_TEMPLATE;
        $sql = self::AddGrSQL($sql, $filter);
        $sql = self::AddStockGrSQL($sql, $filter);
        $sql = self::AddApSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    public static function createFullResultMapping(EntityManager $doctrineEM)
    {
        $rsm = new ResultSetMappingBuilder($doctrineEM);
        $rsm = self::mapDefaultResult($doctrineEM, $rsm);
        $rsm = self::mapGrResult($doctrineEM, $rsm);
        $rsm = self::mapStockGrResult($doctrineEM, $rsm);
        $rsm = self::mapApResult($doctrineEM, $rsm);
        return $rsm;
    }

    public static function createSQLForTotalCount(PoRowReportSqlFilter $filter)
    {
        $sql = PoRowSQLV1::PO_ROW_SQL_TEMPLATE;
        $sql = self::AddGrSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    /*
     * |=============================
     * | Create sql parts.
     * |
     * |=============================
     */
    private static function createSortBy(PoRowReportSqlFilter $filter)
    {
        $tmp = '';
        switch ($filter->getSortBy()) {
            case "itemName":
                $tmp = $tmp . " ORDER BY nmt_inventory_item.item_name " . $filter->getSort();
                break;

            case "poNumber":
                $tmp = $tmp . " ORDER BY nmt_procure_po.sys_number " . $filter->getSort();
                break;

            case "poDate":
                $tmp = $tmp . " ORDER BY nmt_procure_po.doc_date " . $filter->getSort();
                break;

            /**
             *
             * @todo
             */
            case "balance":
                $tmp = $tmp . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.posted_gr_qty,0) " . $filter->getSort();
                break;
        }

        return $tmp;
    }

    private static function createLimitOffset(PoRowReportSqlFilter $filter)
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

    // GR SQL
    private static function AddGrSQL($sql, PoRowReportSqlFilter $filter)
    {
        $select = "
            IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
            IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
            IFNULL(nmt_procure_gr_row.standard_gr_qty,0) AS standard_gr_qty,
            IFNULL(nmt_procure_gr_row.posted_standard_gr_qty,0) AS posted_standard_gr_qty,";

        $sql = str_replace(PoRowSQLV1::SELECT_GR_KEY, $select, $sql);
        $join = sprintf(PoRowSQLV1::PO_POGR_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PoRowSQLV1::JOIN_GR_KEY, $join, $sql);

        return $sql;
    }

    private static function AddStockGrSQL($sql, PoRowReportSqlFilter $filter)
    {
        $select = "
        IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
        IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
        IFNULL(nmt_inventory_trx.standard_stock_gr_qty,0) AS standard_stock_gr_qty,
        IFNULL(nmt_inventory_trx.posted_standard_stock_gr_qty,0) AS posted_standard_stock_gr_qty,";

        $sql = str_replace(PoRowSQLV1::SELECT_STOCK_GR_KEY, $select, $sql);
        $join = sprintf(PoRowSQLV1::PO_STOCK_GR_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PoRowSQLV1::JOIN_STOCK_GR_KEY, $join, $sql);

        return $sql;
    }

    private static function AddApSQL($sql, PoRowReportSqlFilter $filter)
    {
        $select = "
        IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
        IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
        IFNULL(fin_vendor_invoice_row.standard_ap_qty,0) AS standard_ap_qty,
        IFNULL(fin_vendor_invoice_row.posted_standard_ap_qty,0) AS posted_standard_ap_qty,
        IFNULL(fin_vendor_invoice_row.billed_amount,0) AS billed_amount,";

        $sql = str_replace(PoRowSQLV1::SELECT_AP_KEY, $select, $sql);
        $join = sprintf(PoRowSQLV1::PO_AP_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PoRowSQLV1::JOIN_AP_KEY, $join, $sql);
        return $sql;
    }
}
