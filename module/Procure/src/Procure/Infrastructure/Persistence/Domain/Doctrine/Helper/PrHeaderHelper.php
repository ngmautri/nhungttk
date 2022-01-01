<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\SQL\PrHeaderSQL;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderHelper
{

    /**
     * FOR REPORT
     *
     * @param EntityManager $doctrineEM
     * @param PrHeaderReportSqlFilter $filterHeader
     * @param PrRowReportSqlFilter $filterRows
     * @return NULL|mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public static function getPRHeader(EntityManager $doctrineEM, PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filterHeader instanceof PrHeaderReportSqlFilter) {
            return null;
        }

        if (! $filterRows instanceof PrRowReportSqlFilter) {
            return null;
        }

        $sql = self::createPRSQL($filterHeader, $filterRows);

        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("std_gr_completed", "std_gr_completed");
            $rsm->addScalarResult("std_gr_partial", "std_gr_partial");
            $rsm->addScalarResult("std_ap_completed", "std_ap_completed");
            $rsm->addScalarResult("std_ap_partial", "std_ap_partial");
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public static function createPRSQL(PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        $sql = PrHeaderSQL::PR_SQL;
        $filterRows->setPrId($filterHeader->getPrId());
        $filterRows->setIsActive(1);
        $filterRows->setBalance(100);
        $sql1 = PrRowHelper::createPrRowsSQL($filterRows, false);
        $sql = \sprintf($sql, $sql1);

        $sql = $sql . " AND nmt_procure_pr.id = " . $filterHeader->getPrId();
        // $sql = $sql . " GROUP BY nmt_procure_pr.id";
        return $sql;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PrRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getPRList(EntityManager $doctrineEM, PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filterHeader instanceof PrHeaderReportSqlFilter) {
            return null;
        }

        if (! $filterRows instanceof PrRowReportSqlFilter) {
            return null;
        }

        $sql = self::createListSQL($filterHeader, $filterRows);
        $sql = $sql . self::createSortBy($filterHeader) . self::createLimitOffset($filterHeader);
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("std_gr_completed", "std_gr_completed");
            $rsm->addScalarResult("std_gr_partial", "std_gr_partial");
            $rsm->addScalarResult("std_ap_completed", "std_ap_completed");
            $rsm->addScalarResult("std_ap_partial", "std_ap_partial");
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public static function getPRListTotal(EntityManager $doctrineEM, PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        $sql = self::createListTotalSQL($filterHeader, $filterRows);

        $f = "select count(*) as total_row from (%s) as t ";
        $sql = sprintf($f, $sql);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $rsm->addScalarResult("total_row", "total_row");
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public static function createListSQL(PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        $sql = PrHeaderSQL::PR_SQL;

        $filterRows->setBalance(100); // take all rows.
        $sql1 = PrRowHelper::createSQLWihoutLastAP($filterRows);

        $sql = \sprintf($sql, $sql1);

        if ($filterHeader->getDocStatus() == "all") {
            $filterHeader->docStatus = null;
        }

        if ($filterHeader->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active=  1";
        } elseif ($filterHeader->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active = 0";
        }

        if ($filterHeader->getDocYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_pr.created_on) = %s", $filterHeader->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_pr.id";

        // fullfiled
        if ($filterHeader->getBalance() == 1) {
            $sql = $sql . " HAVING total_row <=  std_gr_completed";
        } elseif ($filterHeader->getBalance() == 2) {
            $sql = $sql . " HAVING total_row >  std_gr_completed";
        }
        return $sql;
    }

    public static function createListTotalSQL(PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        $sql = PrHeaderSQL::PR_SQL;

        $filterRows->setBalance(100); // take all rows.
        $sql1 = PrRowHelper::createSQLWihoutLastAP($filterRows);

        $sql = \sprintf($sql, $sql1);

        if ($filterHeader->getDocStatus() == "all") {
            $filterHeader->docStatus = null;
        }

        if ($filterHeader->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active=  1";
        } elseif ($filterHeader->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active = 0";
        }

        if ($filterHeader->getDocYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_pr.submitted_on) = %s", $filterHeader->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_pr.id";

        // fullfiled
        if ($filterHeader->getBalance() == 1) {
            $sql = $sql . " HAVING total_row <=  std_gr_completed";
        } elseif ($filterHeader->getBalance() == 2) {
            $sql = $sql . " HAVING total_row >  std_gr_completed";
        }
        return $sql;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param array $rows
     * @return Generator
     */
    public static function createGenerator(EntityManager $doctrineEM, $rows)
    {
        if ($rows == null) {
            yield null;
        }

        foreach ($rows as $r) {
            /**@var \Application\Entity\NmtProcurePr $po ;*/

            $doctrineRootEntity = $r[0];

            /**
             *
             * @var PrHeaderDetailDTO $dto ;
             */
            $dto = PrMapper::createSnapshot($doctrineEM, $doctrineRootEntity, new PrHeaderDetailDTO());
            if ($dto == null) {
                continue;
            }

            $dto->totalRows = $r["total_row"];
            $dto->grCompletedRows = $r["std_gr_completed"];
            $dto->apCompletedRows = $r["std_ap_completed"];
            $dto->grPartialCompletedRows = $r["std_gr_partial"];
            $dto->apPartialCompletedRows = $r["std_ap_partial"];
            yield $dto;
        }
    }

    public static function getPRSnapshot(EntityManager $doctrineEM, PrHeaderReportSqlFilter $filterHeader, PrRowReportSqlFilter $filterRows)
    {
        $result = self::getPRHeader($doctrineEM, $filterHeader, $filterRows);

        if ($result == null) {
            return null;
        }

        $doctrineRootEntity = $result[0];

        /**
         *
         * @var PRSnapshot $snapshot ;
         */
        $snapshot = PrMapper::createSnapshot($doctrineEM, $doctrineRootEntity);
        if ($snapshot == null) {
            return null;
        }

        $snapshot->totalRows = $result["total_row"];
        $snapshot->grCompletedRows = $result["std_gr_completed"];
        $snapshot->apCompletedRows = $result["std_ap_completed"];
        $snapshot->grPartialCompletedRows = $result["std_gr_partial"];
        $snapshot->apPartialCompletedRows = $result["std_ap_partial"];
        return $snapshot;
    }

    private static function createSortBy(PrHeaderReportSqlFilter $filter)
    {
        $tmp = '';
        switch ($filter->getSortBy()) {

            case "sysNumber":
                $tmp = $tmp . " ORDER BY nmt_procure_pr.pr_auto_number " . $filter->getSort();
                break;

            case "docDate":
                $tmp = $tmp . " ORDER BY nmt_procure_pr.doc_date " . $filter->getSort();
                break;
            case "createdOn":
                $tmp = $tmp . " ORDER BY nmt_procure_pr.submitted_on " . $filter->getSort();
                break;
        }

        return $tmp;
    }

    private static function createLimitOffset(PrHeaderReportSqlFilter $filter)
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
