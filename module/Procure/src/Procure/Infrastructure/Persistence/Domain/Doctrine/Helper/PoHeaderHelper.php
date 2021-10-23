<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\SQL\PoHeaderSQL;
use Procure\Infrastructure\Persistence\SQL\PrHeaderSQL;
use Procure\Infrastructure\Persistence\SQL\Filter\PoHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PoRowReportSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoHeaderHelper
{

    public static function getPOHeader(EntityManager $doctrineEM, PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filterHeader instanceof PoHeaderReportSqlFilter) {
            return null;
        }

        if (! $filterRows instanceof PoRowReportSqlFilter) {
            return null;
        }

        $sql = PoHeaderHelper::createPOSQL($filterHeader, $filterRows);

        $sql = $sql . ";";

        echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
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

    public static function createPOSQL(PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        $sql = PoHeaderSQL::PO_SQL;
        $filterRows->setPoId($filterHeader->getPrId());
        $filterRows->setIsActive(1);
        $filterRows->setBalance(100);
        $sql1 = PoRowHelper::createPoRowsSQL($filterRows, false);
        $sql = \sprintf($sql, $sql1);

        $sql = $sql . " AND nmt_procure_po.id = " . $filterHeader->getPoId();
        // $sql = $sql . " GROUP BY nmt_procure_po.id";
        return $sql;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PoRowReportSqlFilter $filter
     * @return NULL|NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public static function getPOList(EntityManager $doctrineEM, PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filterHeader instanceof PoHeaderReportSqlFilter) {
            return null;
        }

        if (! $filterRows instanceof PoRowReportSqlFilter) {
            return null;
        }

        $sql = PoHeaderHelper::createListSQL($filterHeader, $filterRows);
        $sql = $sql . PoHeaderHelper::createSortBy($filterHeader) . PoHeaderHelper::createLimitOffset($filterHeader);
        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
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

    public static function getPOListTotal(EntityManager $doctrineEM, PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        $sql = PoHeaderHelper::createListTotalSQL($filterHeader, $filterRows);

        $f = "select count(*) as total_row from (%s) as t ";
        $sql = sprintf($f, $sql);
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_po');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $rsm->addScalarResult("total_row", "total_row");
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public static function createListSQL(PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        $sql = PrHeaderSQL::PR_SQL;

        $filterRows->setBalance(100); // take all rows.
        $sql1 = PrRowHelper::createSQLWihoutLastAP($filterRows);

        $sql = \sprintf($sql, $sql1);

        if ($filterHeader->getDocStatus() == "all") {
            $filterHeader->docStatus = null;
        }

        if ($filterHeader->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($filterHeader->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }

        if ($filterHeader->getDocYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_po.submitted_on) = %s", $filterHeader->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_po.id";

        // fullfiled
        if ($filterHeader->getBalance() == 1) {
            $sql = $sql . " HAVING total_row <=  std_gr_completed";
        } elseif ($filterHeader->getBalance() == 2) {
            $sql = $sql . " HAVING total_row >  std_gr_completed";
        }
        return $sql;
    }

    public static function createListTotalSQL(PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        $sql = PoHeaderSQL::PO_SQL;

        $filterRows->setBalance(100); // take all rows.
        $sql1 = PoRowHelper::createSQLWihoutLastAP($filterRows);

        $sql = \sprintf($sql, $sql1);

        if ($filterHeader->getDocStatus() == "all") {
            $filterHeader->docStatus = null;
        }

        if ($filterHeader->getIsActive() == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($filterHeader->getIsActive() == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }

        if ($filterHeader->getDocYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_po.submitted_on) = %s", $filterHeader->getDocYear());
        }

        $sql = $sql . " GROUP BY nmt_procure_po.id";

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

    public static function getPRSnapshot(EntityManager $doctrineEM, PoHeaderReportSqlFilter $filterHeader, PoRowReportSqlFilter $filterRows)
    {
        $result = PoHeaderHelper::getPRHeader($doctrineEM, $filterHeader, $filterRows);

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

    private static function createSortBy(PoHeaderReportSqlFilter $filter)
    {
        $tmp = '';
        switch ($filter->getSortBy()) {

            case "sysNumber":
                $tmp = $tmp . " ORDER BY nmt_procure_po.sys_number " . $filter->getSort();
                break;

            case "docDate":
                $tmp = $tmp . " ORDER BY nmt_procure_po.doc_date " . $filter->getSort();
                break;
            case "createdOn":
                $tmp = $tmp . " ORDER BY nmt_procure_po.created_on " . $filter->getSort();
                break;
        }

        return $tmp;
    }

    private static function createLimitOffset(PoHeaderReportSqlFilter $filter)
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
