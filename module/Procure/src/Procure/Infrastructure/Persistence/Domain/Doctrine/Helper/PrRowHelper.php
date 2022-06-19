<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\SQL\PrRowSQLV1;
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

        if ($filter->getPrId() == null) {
            return null;
        }

        $sql = self::createFullSQL($filter);
        $sql = $sql . self::createSortBy($filter) . self::createLimitOffset($filter);

        // var_dump($sql);

        $rsm = self::createFullResultMapping($doctrineEM);

        return self::runQuery($doctrineEM, $sql, $rsm);
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

        // create SQL
        $sql = self::createFullSQL($filter);
        $sql = $sql . self::createSortBy($filter) . self::createLimitOffset($filter);
        $rsm = self::createFullResultMapping($doctrineEM);

        return self::runQuery($doctrineEM, $sql, $rsm);
    }

    public static function getTotalRows(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }

        $filter->resetLimitOffset();

        $sql = self::createSQLForTotalCount($filter);
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
    public static function createJoinWhere(PrRowReportSqlFilter $filter)
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

        return $tmp1;
    }

    public static function createWhere(PrRowReportSqlFilter $filter)
    {
        $tmp2 = '';

        if ($filter->getDocYear() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND year(nmt_procure_pr.created_on) =%s", $filter->getDocYear());
        }

        if ($filter->getDocMonth() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND year(nmt_procure_pr.created_on) =%s", $filter->getDocMonth());
        }

        if ($filter->getItemId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_inventory_item.id  =%s", $filter->getItemId());
        }

        switch ($filter->getBalance()) {
            case 0:
                $tmp2 = $tmp2 . " HAVING nmt_procure_pr_row.converted_standard_quantity <=  posted_standard_gr_qty";
                break;
            case 1:
                $tmp2 = $tmp2 . " HAVING nmt_procure_pr_row.converted_standard_quantity >  posted_standard_gr_qty";
                break;
        }

        if ($filter->getPrId() > 0) {
            $tmp2 = $tmp2 . \sprintf(" AND nmt_procure_pr_row.pr_id =%s", $filter->getPrId());
        }

        return $tmp2;
    }

    public static function createPrRowsSQL(PrRowReportSqlFilter $filter, $includeLastAP = true)
    {
        $sql = PrRowSQLV1::PR_ROW_SQL_TEMPLATE;
        $sql = self::AddQoSQL($sql, $filter);
        $sql = self::AddPoSQL($sql, $filter);
        $sql = self::AddGrSQL($sql, $filter);
        $sql = self::AddStockGrSQL($sql, $filter);
        $sql = self::AddApSQL($sql, $filter);

        if ($includeLastAP) {
            $sql = self::AddLastApSQL($sql, $filter);
        }
        return sprintf($sql, self::createWhere($filter));
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

            if (isset($r["qo_qty"])) {
                $localSnapshot->qoQuantity = $r["qo_qty"];
            }

            if (isset($r["standard_qo_qty"])) {
                $localSnapshot->standardQoQuantity = $r["standard_qo_qty"];
            }

            if (isset($r["posted_qo_qty"])) {
                $localSnapshot->postedQoQuantity = $r["posted_qo_qty"];
            }

            if (isset($r["posted_standard_qo_qty"])) {
                $localSnapshot->postedStandardQoQuantity = $r["posted_standard_qo_qty"];
            }

            if (isset($r["po_qty"])) {
                $localSnapshot->draftPoQuantity = $r["po_qty"];
            }

            if (isset($r["standard_po_qty"])) {
                $localSnapshot->standardPoQuantity = $r["standard_po_qty"];
            }

            if (isset($r["posted_po_qty"])) {
                $localSnapshot->postedPoQuantity = $r["posted_po_qty"];
            }

            if (isset($r["posted_standard_po_qty"])) {
                $localSnapshot->postedStandardPoQuantity = $r["posted_standard_po_qty"];
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

            if (isset($r["last_vendor_name"])) {
                $localSnapshot->setLastVendorName($r["last_vendor_name"]);
            }

            if (isset($r["last_unit_price"])) {
                $localSnapshot->setLastUnitPrice($r["last_unit_price"]);
            }

            if (isset($r["last_standard_unit_price"])) {
                $localSnapshot->setLastStandardUnitPrice($r["last_standard_unit_price"]);
            }

            if (isset($r["last_standard_convert_factor"])) {
                $localSnapshot->setLastStandardConvertFactor($r["last_standard_convert_factor"]);
            }

            if (isset($r["last_currency_iso3"])) {
                $localSnapshot->setLastCurrency($r["last_currency_iso3"]);
            }

            $localEntity = PRRow::constructFromDB($localSnapshot);

            yield $localEntity;
        }
    }

    public static function createDefaultRowsGenerator(EntityManager $doctrineEM, $rows)
    {
        if ($rows == null) {
            yield null;
        }

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = PrMapper::createRowSnapshot($doctrineEM, $localEnityDoctrine);
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

    private static function mapDefaultResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
        $rsm->addScalarResult("pr_qty", "pr_qty");
        $rsm->addScalarResult("pr_name", "pr_name");
        $rsm->addScalarResult("pr_year", "pr_year");
        $rsm->addScalarResult("item_name", "item_name");
        return $rsm;
    }

    private static function mapQoResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("qo_qty", "qo_qty");
        $rsm->addScalarResult("posted_qo_qty", "posted_qo_qty");
        $rsm->addScalarResult("standard_qo_qty", "standard_qo_qty");
        $rsm->addScalarResult("posted_standard_qo_qty", "posted_standard_qo_qty");
        return $rsm;
    }

    private static function mapPoResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("po_qty", "po_qty");
        $rsm->addScalarResult("posted_po_qty", "posted_po_qty");
        $rsm->addScalarResult("standard_po_qty", "standard_po_qty");
        $rsm->addScalarResult("posted_standard_po_qty", "posted_standard_po_qty");
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
        return $rsm;
    }

    private static function mapLastApResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("last_vendor_name", "last_vendor_name");
        $rsm->addScalarResult("last_unit_price", "last_unit_price");
        $rsm->addScalarResult("last_standard_unit_price", "last_standard_unit_price");
        $rsm->addScalarResult("last_currency_iso3", "last_currency_iso3");
        return $rsm;
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
    public static function createFullSQL(PrRowReportSqlFilter $filter)
    {
        $sql = PrRowSQLV1::PR_ROW_SQL_TEMPLATE;
        $sql = self::AddQoSQL($sql, $filter);
        $sql = self::AddPoSQL($sql, $filter);
        $sql = self::AddGrSQL($sql, $filter);
        $sql = self::AddStockGrSQL($sql, $filter);
        $sql = self::AddApSQL($sql, $filter);
        $sql = self::AddLastApSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    public static function createFullResultMapping(EntityManager $doctrineEM)
    {
        $rsm = new ResultSetMappingBuilder($doctrineEM);
        $rsm = self::mapDefaultResult($doctrineEM, $rsm);
        $rsm = self::mapQoResult($doctrineEM, $rsm);
        $rsm = self::mapPoResult($doctrineEM, $rsm);
        $rsm = self::mapGrResult($doctrineEM, $rsm);
        $rsm = self::mapStockGrResult($doctrineEM, $rsm);
        $rsm = self::mapApResult($doctrineEM, $rsm);
        $rsm = self::mapLastApResult($doctrineEM, $rsm);
        return $rsm;
    }

    public static function createSQLWihoutLastAP(PrRowReportSqlFilter $filter)
    {
        $sql = PrRowSQLV1::PR_ROW_SQL_TEMPLATE;
        $sql = self::AddQoSQL($sql, $filter);
        $sql = self::AddPoSQL($sql, $filter);
        $sql = self::AddGrSQL($sql, $filter);
        $sql = self::AddStockGrSQL($sql, $filter);
        $sql = self::AddApSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    public static function createSQLForTotalCount(PrRowReportSqlFilter $filter)
    {
        $sql = PrRowSQLV1::PR_ROW_SQL_TEMPLATE;
        $sql = self::AddGrSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
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

    private static function AddQoSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select_qo = "
        IFNULL(nmt_procure_qo_row.qo_qty,0) AS qo_qty,
        IFNULL(nmt_procure_qo_row.posted_qo_qty,0) AS posted_qo_qty,
        IFNULL(nmt_procure_qo_row.standard_qo_qty,0) AS standard_qo_qty,
        IFNULL(nmt_procure_qo_row.posted_standard_qo_qty,0) AS posted_standard_qo_qty,";

        $sql = str_replace(PrRowSQLV1::SELECT_QO_KEY, $select_qo, $sql);
        $sql1 = sprintf(PrRowSQLV1::PR_QO_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_QO_KEY, $sql1, $sql);

        return $sql;
    }

    private static function AddPoSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select = "
        IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
        IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
        IFNULL(nmt_procure_po_row.standard_po_qty,0) AS standard_po_qty,
        IFNULL(nmt_procure_po_row.posted_standard_po_qty,0) AS posted_standard_po_qty,";

        $sql = str_replace(PrRowSQLV1::SELECT_PO_KEY, $select, $sql);
        $join = sprintf(PrRowSQLV1::PR_PO_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_PO_KEY, $join, $sql);

        return $sql;
    }

    private static function AddGrSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select = "
            IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
            IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
            IFNULL(nmt_procure_gr_row.standard_gr_qty,0) AS standard_gr_qty,
            IFNULL(nmt_procure_gr_row.posted_standard_gr_qty,0) AS posted_standard_gr_qty,";

        $sql = str_replace(PrRowSQLV1::SELECT_GR_KEY, $select, $sql);
        $join = sprintf(PrRowSQLV1::PR_POGR_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_GR_KEY, $join, $sql);

        return $sql;
    }

    private static function AddStockGrSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select = "
        IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
        IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
        IFNULL(nmt_inventory_trx.standard_stock_gr_qty,0) AS standard_stock_gr_qty,
        IFNULL(nmt_inventory_trx.posted_standard_stock_gr_qty,0) AS posted_standard_stock_gr_qty,";

        $sql = str_replace(PrRowSQLV1::SELECT_STOCK_GR_KEY, $select, $sql);
        $join = sprintf(PrRowSQLV1::PR_STOCK_GR_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_STOCK_GR_KEY, $join, $sql);

        return $sql;
    }

    private static function AddApSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select = "
        IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
        IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
        IFNULL(fin_vendor_invoice_row.standard_ap_qty,0) AS standard_ap_qty,
        IFNULL(fin_vendor_invoice_row.posted_standard_ap_qty,0) AS posted_standard_ap_qty,";

        $sql = str_replace(PrRowSQLV1::SELECT_AP_KEY, $select, $sql);

        $join = sprintf(PrRowSQLV1::PR_AP_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_AP_KEY, $join, $sql);
        return $sql;
    }

    public static function AddLastApSQL($sql, PrRowReportSqlFilter $filter)
    {
        $select = "
        last_ap.vendor_name as last_vendor_name,
        last_ap.unit_price as last_unit_price,
        last_ap.converted_standard_unit_price as last_standard_unit_price,
        last_ap.standard_convert_factor as last_standard_convert_factor,
        last_ap.currency_iso3 as last_currency_iso3,";

        $sql = str_replace(PrRowSQLV1::SELECT_LAST_AP_KEY, $select, $sql);

        $join = sprintf(PrRowSQLV1::ITEM_LAST_AP_SQL, self::createJoinWhere($filter));
        $sql = str_replace(PrRowSQLV1::JOIN_LAST_AP_KEY, $join, $sql);
        return $sql;
    }
}
