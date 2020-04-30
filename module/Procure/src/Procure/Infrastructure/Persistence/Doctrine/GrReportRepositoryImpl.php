<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Mapper\GrMapper;
use Procure\Infrastructure\Persistence\GrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\GrReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReportRepositoryImpl extends AbstractDoctrineRepository implements GrReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\GrReportRepositoryInterface::getOfItem()
     */
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
     * @see \Procure\Infrastructure\Persistence\GrReportRepositoryInterface::getList()
     */
    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $results = $this->_getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcureGr $po ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = GrMapper::createDetailSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            $rootSnapshot->netAmount = $r["net_amount"];
            $rootSnapshot->taxAmount = $r["tax_amount"];
            $rootSnapshot->grossAmount = $r["gross_amount"];
            $rootSnapshot->discountAmount = $r["gross_discount_amount"];
            $resultList[] = $rootSnapshot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\GrReportRepositoryInterface::getListTotal()
     */
    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        return $this->_getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
    }

    private function _getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = GrReportSQL::GR_LIST;

        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active = 0";
        }

        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_gr.current_state = '" . $current_state . "'";
        }
        if ($docStatus == "all") {
            $docStatus = null;
        }

        if ($docStatus != null) {
            $sql = $sql . " AND nmt_procure_gr.doc_status = '" . $docStatus . "'";
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
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("gross_discount_amount", "gross_discount_amount");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = GrReportSQL::GR_LIST;

        if ($docStatus == "all") {
            $docStatus = null;
        }

        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active = 0";
        }

        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_gr.current_state = '" . $current_state . "'";
        }

        if ($docStatus != null) {
            $sql = $sql . " AND nmt_procure_gr.doc_status = '" . $docStatus . "'";
        }

        $sql = $sql . " GROUP BY nmt_procure_gr.id";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
