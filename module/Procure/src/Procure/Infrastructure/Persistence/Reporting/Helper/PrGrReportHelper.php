<?php
namespace Procure\Infrastructure\Persistence\Reporting\Helper;

use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Filter\PrGrReportSqlFilter;
use Procure\Infrastructure\Persistence\Reporting\SQL\PrGrReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrGrReportHelper
{

    /**
     *
     * @param ProcureAppSqlFilterInterface $filter
     */
    private static function _getListSql(PrGrReportSqlFilter $filter)
    {
        $sql1 = '';
        $sql2 = '';
        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getCompanyId() > 0) {
            $sql1 = $sql1 . \sprintf(' AND nmt_procure_gr.company_id =%s', $filter->getCompanyId());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_pr.company_id =%s', $filter->getCompanyId());
        }

        if ($filter->getWarehouseId() > 0) {
            $sql1 = $sql1 . \sprintf(' AND nmt_procure_gr.warehouse_id =%s', $filter->getWarehouseId());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_pr.warehouse_id =%s', $filter->getWarehouseId());
        }

        if ($filter->getIsActive() == 1) {
            $sql1 = $sql1 . " AND nmt_procure_gr.is_active = 1";
            $sql2 = $sql2 . " AND nmt_procure_pr.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql1 = $sql1 . " AND nmt_procure_gr.is_active = 0";
            $sql2 = $sql2 . " AND nmt_procure_pr.is_active = 0";
        }

        if ($filter->getIsRowActive() == 1) {
            $sql1 = $sql1 . " AND nmt_procure_gr_row.is_active = 1";
            $sql2 = $sql2 . " AND nmt_procure_pr_row.is_active = 1";
        } elseif ($filter->getIsRowActive() == - 1) {
            $sql1 = $sql1 . " AND nmt_procure_gr_row.is_active = 0";
            $sql2 = $sql2 . " AND nmt_procure_pr_row.is_active = 0";
        }

        if ($filter->getDocStatus() != null) {
            $sql1 = $sql1 . \sprintf(' AND nmt_procure_gr.doc_status ="%s"', $filter->getDocStatus());
            $sql2 = $sql2 . \sprintf(' AND nmt_procure_pr.doc_status ="%s"', $filter->getDocStatus());
        }

        if (! $filter->getFromDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_procure_gr.posting_date >='%s'", $filter->getFromDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_procure_pr.submitted_on >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_procure_gr.posting_date <='%s'", $filter->getToDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_procure_pr.submitted_on <='%s'", $filter->getToDate());
        }

        // SQL;
        $sql = PrGrReportSQL::REPORT_LIST;

        $sql = \sprintf($sql, $sql1, $sql2);

        return $sql;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param ProcureAppSqlFilterInterface $filter
     * @throws \InvalidArgumentException
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getList(EntityManager $doctrineEM, ProcureAppSqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof PrGrReportSqlFilter) {
            throw new \InvalidArgumentException('PrGrReportSqlFilter expected!');
        }

        $sql_tmp = PrGrReportHelper::_getListSql($filter);

        $sql = \sprintf('select * from (%s) as t', $sql_tmp);

        $sort = SqlKeyWords::ASC;
        if ($filter->getSort() !== null) {
            $sort = $filter->getSort();
        }

        // \var_dump($filter);

        switch ($filter->getSortBy()) {
            case "warehouseName":
                $sql = $sql . \sprintf(" ORDER BY t.warehouseName %s", $sort);
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
            $rsm->addScalarResult("companyId", "companyId");
            $rsm->addScalarResult("docTypeName", "docTypeName");
            $rsm->addScalarResult("docType", "docType");
            $rsm->addScalarResult("vendorId", "vendorId");
            $rsm->addScalarResult("vendorName", "vendorName");
            $rsm->addScalarResult("docId", "docId");
            $rsm->addScalarResult("docDate", "docDate");
            $rsm->addScalarResult("docNumber", "docNumber");
            $rsm->addScalarResult("docSysNumber", "docSysNumber");
            $rsm->addScalarResult("docStatus", "docStatus");
            $rsm->addScalarResult("docIsActive", "docIsActive");
            $rsm->addScalarResult("docPostingDate", "docPostingDate");
            // $rsm->addScalarResult("doc_currency", "docCurrency");

            $rsm->addScalarResult("departmentId", "departmentId");
            $rsm->addScalarResult("warehouseId", "warehouseId");
            $rsm->addScalarResult("warehouseCode", "warehouseCode");
            $rsm->addScalarResult("warehouseName", "warehouseName");

            $rsm->addScalarResult("rowId", "rowId");
            $rsm->addScalarResult("rowIdentifer", "rowIdentifer");
            $rsm->addScalarResult("itemId", "itemId");
            $rsm->addScalarResult("itemName", "itemName");
            $rsm->addScalarResult("itemSku", "itemSku");
            $rsm->addScalarResult("itemSysNumber", "itemSysNumber");

            $rsm->addScalarResult("itemStandardUom", "itemStandardUom");
            $rsm->addScalarResult("itemStandardUomName", "itemStandardUomName");

            $rsm->addScalarResult("rowDocQuantity", "rowDocQuantity");
            $rsm->addScalarResult("rowStandardConvertFactor", "rowStandardConvertFactor");
            $rsm->addScalarResult("rowDocUnit", "rowDocUnit");
            $rsm->addScalarResult("rowDocUnitPrice", "rowDocUnitPrice");
            $rsm->addScalarResult("rowIsActive", "rowIsActive");
            $rsm->addScalarResult("convertedStandardQuantity", "convertedStandardQuantity");
            $rsm->addScalarResult("convertedStandardUnitPrice", "convertedStandardUnitPrice");

            $rsm->addScalarResult("prRowId", "prRowId");

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
