<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Procure\Infrastructure\Persistence\POListRepositoryInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\NoResultException;
use Procure\Infrastructure\Persistence\SQL\PoSQL;
use Procure\Infrastructure\Mapper\PoMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POListRepository extends AbstractDoctrineRepository implements POListRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPoList()
     */
    public function getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $results = $this->_getPoList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {
            /**@var \Application\Entity\NmtProcurePo $po ;*/
            $po = $r[0];

            $poDetailsSnapshot = PoMapper::createDetailSnapshot($po);

            if ($poDetailsSnapshot == null) {
                continue;
            }

            $poDetailsSnapshot->totalRows = $r["total_row"];
            $poDetailsSnapshot->totalActiveRows = $r["active_row"];
            $poDetailsSnapshot->netAmount = $r["net_amount"];
            $poDetailsSnapshot->taxAmount = $r["tax_amount"];
            $poDetailsSnapshot->grossAmount = $r["gross_amount"];
            $poDetailsSnapshot->billedAmount = $r["billed_amount"];

            // $poDetailsSnapshot->discountAmount = $r["draft_gr_qty"];
            // $poDetailsSnapshot->completedRows = $r["draft_gr_qty"];

            $resultList[] = $poDetailsSnapshot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPOStatus()
     */
    public function getPOStatus($id, $token)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\POListRepositoryInterface::getPoOfItem()
     */
    public function getPoOfItem($item_id, $token)
    {}

    public function getPoRowOfVendor($vendor_id = null, $vendor_token = null, $sort_by = null, $order = 'DESC', $limit = 0, $offset = 0)
    {}

    public function getOpenPoAP($id, $token)
    {}

    public function getOpenPoGr($id, $token)
    {}

   
    private function _getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = PoSQL::PO_LIST;

        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }

        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_po.current_state = '" . $current_state . "'";
        }

        if ($docStatus != null) {
            $sql = $sql . " AND nmt_procure_po.doc_status = '" . $docStatus . "'";
        }

        $sql = $sql . " GROUP BY nmt_procure_po.id";

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_procure_po.sys_number " . $sort;
                break;

            case "poDate":
                $sql = $sql . " ORDER BY nmt_procure_po.contract_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_po.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY nmt_procure_po.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY nmt_procure_po.currency_iso3 " . $sort;
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
