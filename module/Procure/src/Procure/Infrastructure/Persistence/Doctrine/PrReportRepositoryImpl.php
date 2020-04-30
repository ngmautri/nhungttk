<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\PrSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReportRepositoryImpl extends AbstractDoctrineRepository implements PrReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getListWithCustomDTO()
     */
    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = $this->_getList($filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePr $po ;*/

            $doctrineRootEntity = $r[0];

            /**
             *
             * @var PrHeaderDetailDTO $dto ;
             */
            $dto = PrMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity, new PrHeaderDetailDTO());
            if ($dto == null) {
                continue;
            }

            $dto->totalRows = $r["total_row"];
            $dto->grCompletedRows = $r["gr_completed"];
            $dto->apCompletedRows = $r["ap_completed"];
            $dto->grPartialCompletedRows = $r["gr_partial_completed"];
            $dto->apPartialCompletedRows = $r["ap_partial_completed"];
            $resultList[] = $dto;
        }
        return $resultList;
    }

    public function getOfItem($itemId, $itemToken)
    {
        $sql = "
SELECT
    nmt_inventory_item.item_name as item_name,
	nmt_procure_qo_row.*
FROM nmt_procure_qo_row
            
LEFT JOIN nmt_procure_qo
ON nmt_procure_qo.id = nmt_procure_qo_row.qo_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_qo_row.item_id
WHERE 1            
";

        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;

        $sql = $sql . sprintf(" AND nmt_inventory_item.id =%s AND nmt_inventory_item.token='%s'", $item_id, $token);
        $sql = $sql . " ORDER BY nmt_procure_qo.contract_date DESC ";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQoRow', 'nmt_procure_qo_row');
            $rsm->addScalarResult("item_name", "item_name");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = $this->_getList($filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePr $po ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = PrMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            $resultList[] = $rootSnapshot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object");
        }

        return $this->_getListTotal($filter);
    }

    private function _getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
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

        if ($filter->getPrYear() > 0) {
            $sql = $sql . \sprintf(" AND year(nmt_procure_pr.submitted_on) = %s", $filter->getPrYear());
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
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("gr_completed", "gr_completed");
            $rsm->addScalarResult("ap_completed", "ap_completed");
            $rsm->addScalarResult("gr_partial_completed", "gr_partial_completed");
            $rsm->addScalarResult("ap_partial_completed", "ap_partial_completed");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getListTotal(SqlFilterInterface $filter)
    {
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

        if ($filter->getPrYear() > 0) {
            $sql = $sql . sprintf(" AND year(nmt_procure_pr.submitted_on) =%s", $filter->getPrYear());
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
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("gr_completed", "gr_completed");
            $rsm->addScalarResult("ap_completed", "ap_completed");
            $rsm->addScalarResult("gr_partial_completed", "gr_partial_completed");
            $rsm->addScalarResult("ap_partial_completed", "ap_partial_completed");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return null;
        }
    }
}