<?php
namespace Procure\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Filter\GrReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\GrReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReportHelper
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

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }

        $sql = GrReportSQL::GR_LIST;

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active = 0";
        }

        if ($filter->getCurrentState() != null) {
            $sql = $sql . " AND nmt_procure_gr.current_state = '" . $filter->getCurrentState() . "'";
        }
        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getDocStatus() != null) {
            $sql = $sql . " AND nmt_procure_gr.doc_status = '" . $filter->getDocStatus() . "'";
        }

        $sql = $sql . " GROUP BY nmt_procure_gr.id";

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_procure_gr.sys_number " . $sort;
                break;

            case "docDate":
                $sql = $sql . " ORDER BY nmt_procure_gr.doc_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_gr.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY nmt_procure_gr.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY nmt_procure_gr.currency_iso3 " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("gross_discount_amount", "gross_discount_amount");

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

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
        $sql = GrReportSQL::GR_LIST;

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active=  1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active = 0";
        }

        if ($filter->getCurrentState() != null) {
            $sql = $sql . " AND nmt_procure_gr.current_state = '" . $filter->getCurrentState() . "'";
        }
        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getDocStatus() != null) {
            $sql = $sql . " AND nmt_procure_gr.doc_status = '" . $filter->getDocStatus() . "'";
        }

        $sql = $sql . " GROUP BY nmt_procure_gr.id";

        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
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

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @return NULL|number
     */
    static public function getAllRowTotal(EntityManager $doctrineEM, SqlFilterInterface $filter)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof GrReportSqlFilter) {
            return null;
        }
    }
}
