<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Mapper\PoMapper;
use Procure\Infrastructure\Persistence\POListRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\PoSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POListRepositoryImpl extends AbstractDoctrineRepository implements POListRepositoryInterface
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

            $poDetailsSnapshot = PoMapper::createDetailSnapshot($this->doctrineEM, $po);

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

    public function getAllPoRowStatus($is_active = 1, $po_year, $balance = 1, $sort_by, $sort, $limit, $offset)
    {
        $results = $this->_getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $entity ;*/
            $entity = $r[0];

            $poRowDetailsSnapshot = PoMapper::createRowDetailSnapshot($this->getDoctrineEM(), $entity);

            if ($poRowDetailsSnapshot == null) {
                continue;
            }

            $poRowDetailsSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $poRowDetailsSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $poRowDetailsSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $poRowDetailsSnapshot->openGrBalance = $r["open_gr_qty"];
            $poRowDetailsSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $poRowDetailsSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $poRowDetailsSnapshot->openAPQuantity = $r["open_ap_qty"];
            $poRowDetailsSnapshot->billedAmount = $r["billed_amount"];
            $poRowDetailsSnapshot->openAPAmount = $poRowDetailsSnapshot->netAmount - $poRowDetailsSnapshot->billedAmount;

            $resultList[] = $poRowDetailsSnapshot;
        }

        return $resultList;
    }

    private function _getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = PoSQL::PO_LIST;

        if ($docStatus == "all") {
            $docStatus = null;
        }

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

    private function _getAllPoRowStatus($is_active = 1, $po_year, $balance = 1, $sort_by, $sort, $limit, $offset)
    {
        $sql = "
SELECT
nmt_procure_po_row.*,
fin_vendor_invoice_row.*,
nmt_procure_gr_row.draft_gr_qty,
nmt_procure_gr_row.posted_gr_qty,

nmt_procure_gr_row.confirmed_gr_balance,
nmt_procure_gr_row.open_gr_qty

FROM nmt_procure_po_row

LEFT JOIN nmt_procure_po
on nmt_procure_po.id = nmt_procure_po_row.po_id            
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
            
LEFT JOIN
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
            
WHERE 1 AND nmt_procure_po_row.is_active=1 AND nmt_procure_po.doc_status='posted'%s";
        /**
         *
         * @todo To add Return and Credit Memo
         */

        $sql_tmp = '';
        $sql_tmp1 = '';

        if ($po_year > 0) {
            $sql_tmp = $sql_tmp . " AND year(nmt_procure_po.contract_date) =" . $po_year;
        }

        if ($balance == 0) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) <= 0";
        }
        if ($balance == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) > 0";
        }
        if ($balance == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_po_row.quantity -  IFNULL(fin_vendor_invoice_row.posted_ap_qty,0)) < 0";
        }

        switch ($sort_by) {
            /*
             * case "itemName":
             * $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_inventory_item.item_name " . $sort;
             * break;
             */
            case "vendorName":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_po.vendor_name " . $sort;
                break;

            case "poNumber":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_po_row.contract_no " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp1 = $sql_tmp1 . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp1 = $sql_tmp1 . " OFFSET " . $offset;
        }

        $sql1 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_AP, $sql_tmp);
        $sql2 = sprintf(\Procure\Infrastructure\Doctrine\SQL\PoSQL::SQL_ROW_PO_GR, $sql_tmp);

        $sql = sprintf($sql, $sql1, $sql2, $sql_tmp . $sql_tmp1);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("draft_gr_qty", "draft_gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            $rsm->addScalarResult("confirmed_gr_balance", "confirmed_gr_balance");
            $rsm->addScalarResult("open_gr_qty", "open_gr_qty");
            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getAllPoRowStatusTotal($is_active = 1, $po_year, $balance = 1)
    {
        $results = $this->_getAllPoRowStatus($is_active, $po_year, $balance, null, null, null, null);
        return count($results);
    }
}
