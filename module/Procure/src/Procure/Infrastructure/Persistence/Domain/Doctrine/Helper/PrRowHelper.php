<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Persistence\SQL\PrRowSQL;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowHelper
{

    public static function getRowsByPrId(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }

        $tmp1 = '';
        $tmp2 = '';

        if ($filter->getPrId() > 0) {
            $tmp1 .= sprintf(" AND nmt_procure_pr_row.pr_id=%s", $filter->getPrId());
        }
        if ($filter->getItemId() > 0) {
            $tmp1 .= sprintf(" AND nmt_procure_pr_row.item_id=%s", $filter->getItemId());
        }

        if ($filter->getPrId() > 0) {
            $tmp2 .= sprintf(" AND nmt_procure_pr.id=%s", $filter->getPrId());
        }

        $sql = self::createSQL($tmp1, $tmp2);

        $sql . self::createSortBy($filter) . self::createLimitOffset($filter);

        echo $sql;

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
