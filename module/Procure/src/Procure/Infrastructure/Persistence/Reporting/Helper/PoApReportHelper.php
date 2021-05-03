<?php
namespace Procure\Infrastructure\Persistence\Reporting\Helper;

use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Filter\PoApReportSqlFilter;
use Procure\Infrastructure\Persistence\Reporting\SQL\PoApReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoApReportHelper
{

    /**
     *
     * @param ProcureAppSqlFilterInterface $filter
     */
    private static function _getListSql(ProcureAppSqlFilterInterface $filter)
    {
        if (! $filter instanceof PoApReportSqlFilter) {
            throw new \InvalidArgumentException('PoApReportSqlFilter expected!');
        }

        $sql1 = '';
        $sql2 = '';
        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getCompanyId() > 0) {
            $sql1 = $sql1 . \sprintf(' AND fin_vendor_invoice.company_id =%s', $filter->getCompanyId());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_po.company_id =%s', $filter->getCompanyId());
        }

        if ($filter->getVendorId() > 0) {
            $sql1 = $sql1 . \sprintf(' AND fin_vendor_invoice.vendor_id =%s', $filter->getVendorId());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_po.vendor_id =%s', $filter->getVendorId());
        }

        if ($filter->getIsActive() == 1) {
            $sql1 = $sql1 . " AND fin_vendor_invoice.is_active = 1";
            $sql2 = $sql2 . " AND nmt_procure_po.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql1 = $sql1 . " AND fin_vendor_invoice.is_active = 0";
            $sql2 = $sql2 . " AND nmt_procure_po.is_active = 0";
        }

        if ($filter->getIsRowActive() == 1) {
            $sql1 = $sql1 . " AND fin_vendor_invoice_row.is_active = 1";
            $sql2 = $sql2 . " AND nmt_procure_po_row.is_active = 1";
        } elseif ($filter->getIsRowActive() == - 1) {
            $sql1 = $sql1 . " AND fin_vendor_invoice_row.is_active = 0";
            $sql2 = $sql2 . " AND nmt_procure_po_row.is_active = 0";
        }

        if ($filter->getDocStatus() != null) {
            $sql1 = $sql1 . \sprintf(' AND fin_vendor_invoice.doc_status ="%s"', $filter->getDocStatus());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_po.doc_status ="%s"', $filter->getDocStatus());
        }

        if (! $filter->getFromDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND fin_vendor_invoice.posting_date >='%s'", $filter->getFromDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_procure_po.doc_date >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND fin_vendor_invoice.posting_date <='%s'", $filter->getToDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_procure_po.doc_date <='%s'", $filter->getToDate());
        }

        // SQL;
        $sql = PoApReportSQL::REPORT_LIST;

        $sql = \sprintf($sql, $sql1, $sql2);

        return $sql;
    }

    static public function getList(EntityManager $doctrineEM, ProcureAppSqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PoApReportSqlFilter) {
            throw new \InvalidArgumentException('PoApReportSqlFilter expected!');
        }

        $sql_tmp = self::_getListSql($filter);

        $sql = \sprintf('select * from (%s) as t', $sql_tmp);

        $sort = SqlKeyWords::ASC;
        if ($filter->getSort() !== null) {
            $sort = $filter->getSort();
        }

        // \var_dump($filter);

        switch ($filter->getSortBy()) {
            case "vendorName":
                $sql = $sql . \sprintf(" ORDER BY t.vendor_name %s", $sort);
                break;
        }
        if ($filter->getLimit() > 0) {
            $sql = $sql . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $sql = $sql . " OFFSET " . $filter->getOffset();
        }

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            // $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryMv', 'nmt_inventory_mv');
            $rsm->addScalarResult("company_id", "companyId");
            $rsm->addScalarResult("doc_type_name", "docTypeName");
            $rsm->addScalarResult("doc_type", "docType");
            $rsm->addScalarResult("vendor_id", "vendorId");
            $rsm->addScalarResult("vendor_name", "vendorName");
            $rsm->addScalarResult("doc_id", "docId");
            $rsm->addScalarResult("doc_number", "docNumber");
            $rsm->addScalarResult("doc_sys_number", "docSysNumber");
            $rsm->addScalarResult("doc_status", "docStatus");
            $rsm->addScalarResult("doc_is_active", "docIsActive");
            $rsm->addScalarResult("doc_currency", "docCurrency");

            $rsm->addScalarResult("row_id", "rowId");
            $rsm->addScalarResult("row_identifer", "rowIdentifer");
            $rsm->addScalarResult("item_id", "itemId");
            $rsm->addScalarResult("item_name", "itemName");
            $rsm->addScalarResult("item_sku", "itemSku");
            $rsm->addScalarResult("item_sys_number", "itemSysNumber");

            $rsm->addScalarResult("row_doc_quantity", "rowDocQuantity");
            $rsm->addScalarResult("row_standard_convert_factor", "rowStandardConvertFactor");
            $rsm->addScalarResult("row_doc_unit", "rowDocUnit");
            $rsm->addScalarResult("row_doc_unit_price", "rowDocUnitPrice");
            $rsm->addScalarResult("row_is_active", "rowIsActive");
            $rsm->addScalarResult("converted_standard_quantity", "convertedStandardQuantity");
            $rsm->addScalarResult("converted_standard_unit_price", "convertedStandardUnitPrice");

            $rsm->addScalarResult("pr_row_id", "prRowId");
            $rsm->addScalarResult("po_row_id", "poRowId");
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
     * @param ProcureAppSqlFilterInterface $filter
     * @return NULL|mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    static public function getListTotal(EntityManager $doctrineEM, ProcureAppSqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $sql_tmp = self::_getListSql($filter);

        $sql = \sprintf('select count(*) as total from (%s) as t', $sql_tmp);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $rsm->addScalarResult("total", "total");
            $result = $query->getSingleScalarResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
