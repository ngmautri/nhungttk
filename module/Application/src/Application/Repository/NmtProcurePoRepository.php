<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NmtProcurePoRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtProcurePo $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePoRepository")
    private $sql = "
SELECT
	nmt_procure_po.*,
	COUNT(nmt_procure_po_row.id) AS active_row,
    COUNT(nmt_procure_po_row.id) AS total_row,
	MAX(nmt_procure_po_row.row_number) AS max_row_number,
  	SUM(nmt_procure_po_row.net_amount) AS net_amount,
    SUM(nmt_procure_po_row.tax_amount) AS tax_amount,
	SUM(nmt_procure_po_row.gross_amount) AS gross_amount, 
	SUM(nmt_procure_po_row.billed_amount) AS billed_amount
FROM nmt_procure_po

LEFT JOIN
(  
       SELECT
		fin_vendor_invoice_row.id AS invoice_row_id,
        nmt_procure_po_row.*,
        SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS billed_amount,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.quantity) ELSE 0 END) AS billed_qty
    FROM nmt_procure_po_row	
    LEFT JOIN fin_vendor_invoice_row
	ON nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id
    WHERE nmt_procure_po_row.is_active =1
   GROUP BY nmt_procure_po_row.id
   
)AS 
nmt_procure_po_row
ON nmt_procure_po.id = nmt_procure_po_row.po_id

WHERE 1
";

    public function getPo($po_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = $this->sql;
        
        $sql = $sql . " AND nmt_procure_po.id =" . $po_id . " AND nmt_procure_po.token='" . $token . "' GROUP BY nmt_procure_po.id";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getPoList($is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_po.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_po.current_state = '" . $current_state . "'";
        }
        
        $sql = $sql . " GROUP BY nmt_procure_po.id";
        
        switch ($sort_by) {
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
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getPoOf($vendor_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if ($vendor_id > 0) {
            $sql = $sql . " AND nmt_procure_po.vendor_id =" . $vendor_id;
        } else {
            return null;
        }
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND v.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_po.current_state = '" . $current_state . "'";
        }
        
        $sql = $sql . " GROUP BY nmt_procure_po.id";
        
        switch ($sort_by) {
            case "poDate":
                $sql = $sql . " ORDER BY nmt_procure_po.contract_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (fnmt_procure_po_row.gross_amount) ELSE 0 END) " . $sort;
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
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePo', 'nmt_procure_po');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function downLoadVendorPo($po_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = "
SELECT 
nmt_procure_po_row.*
FROM nmt_procure_po_row
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
WHERE 1
";
        
        $sql = $sql . " AND nmt_procure_po_row.is_active=1 AND nmt_procure_po.id =" . $po_id . " AND nmt_procure_po.token='" . $token . "'";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getPoOfItem($item_id, $token)
    {
        $sql = "
SELECT
    nmt_inventory_item.item_name as item_name,
	nmt_procure_po_row.*
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id
WHERE 1
            
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = $sql . sprintf(" AND nmt_inventory_item.id =%s AND nmt_inventory_item.token='%s'", $item_id, $token);
        $sql = $sql . " ORDER BY nmt_procure_po.contract_date DESC ";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'procure_po_row');
            $rsm->addScalarResult("item_name", "item_name");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get Open Po
     */
    public function getOpenPoGr($id, $token)
    {
        $sql = "
SELECT
    nmt_procure_gr_row.id AS gr_row_id,
    nmt_procure_gr_row.po_row_id AS po_row_id,
	IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS draft_gr,
    IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_gr,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END)-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS open_gr,
    nmt_procure_po_row.*
FROM nmt_procure_po_row
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id =  nmt_procure_po_row.id
WHERE nmt_procure_po_row.po_id=%s
GROUP BY nmt_procure_po_row.id            
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = sprintf($sql, $id);
        
        // echo $sql;
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("draft_gr", "draft_gr");
            $rsm->addScalarResult("confirmed_gr", "confirmed_gr");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("open_gr", "open_gr");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function updatePo($id, $token = null)
    {
        $message = "";
        
        try {
            $po_rows = $this->getOpenPoGr($id, $token);
            if (count($po_rows) > 0) {
                $total_open_gr = 0;
                
                /**@var \Application\Entity\NmtProcurePoRow $po;*/
                $po = null;
                foreach ($po_rows as $r) {
                    
                    /**@var \Application\Entity\NmtProcurePoRow $po_row ;*/
                    $po_row = $r[0];
                    
                    $total_open_gr = $total_open_gr + $r['open_gr'];
                    
                    // close row
                    if ($r['confirmed_balance'] == 0) {
                        $po_row->setCurrentState("COMPLETED");
                    }
                    $po = $po_row->getPo();
                    $this->_em->persist($po_row);
                }
                
                if ($total_open_gr == 0) {
                    $po->setCurrentState("COMPLETED");
                } else {
                    $po->setCurrentState("OPEN");
                }
                $this->_em->persist($po);
                
                $this->_em->flush();
                
                $message = sprintf("[OK] PO #%s updated", $po->getSysNumber());
                return $message;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            return $message;
        }
    }

    /**
     */
    public function updatePOofGR($id)
    {
        $sql = "
SELECT
nmt_procure_gr.*,
nmt_procure_po_row.po_id

FROM nmt_procure_gr

left join nmt_procure_gr_row
on nmt_procure_gr.id = nmt_procure_gr_row.gr_id

left join nmt_procure_po_row
on nmt_procure_po_row.id = nmt_procure_gr_row.po_row_id

where nmt_procure_gr.id=%s
group by nmt_procure_po_row.po_id
	
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = sprintf($sql, $id);
        
        // echo $sql;
        $message = array();
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            $rsm->addScalarResult("po_id", "po_id");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            
            foreach ($result as $r) {
                
                $m = $this->updatePo($r['po_id']);
                $message[] = $m;
            }
            
            return $message;
        } catch (NoResultException $e) {
            
            $message[] = $e->getMessage();
            return $message;
        }
    }

    public function getGrOfPoRow($id, $token)
    {
        $sql = "
SELECT
	nmt_procure_gr_row.*
FROM nmt_procure_gr_row
left join nmt_procure_po_row
on nmt_procure_po_row.id = nmt_procure_gr_row.po_row_id
where nmt_procure_gr_row.po_row_id=%s	
	
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = sprintf($sql, $id);
        
        // echo $sql;
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    // ===================================
    /**
     *
     * @todo GOODS RECEIPT
     */
    public function getGr($id, $token)
    {
        $sql = " 
SELECT
    nmt_procure_gr.*,
    count(nmt_procure_gr_row.id) as total_row
   FROM nmt_procure_gr
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.gr_id = nmt_procure_gr.id
WHERE 1
";
        
        $sql = sprintf($sql . " AND nmt_procure_gr.id = %s AND nmt_procure_gr.token='%s' Group BY nmt_procure_gr_row.gr_id", $id, $token);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            $rsm->addScalarResult("total_row", "total_row");
            
            /*
             * $rsm->addScalarResult("active_row", "active_row");
             * $rsm->addScalarResult("total_row", "total_row");
             * $rsm->addScalarResult("max_row_number", "max_row_number");
             * $rsm->addScalarResult("net_amount", "net_amount");
             * $rsm->addScalarResult("tax_amount", "tax_amount");
             * $rsm->addScalarResult("gross_amount", "gross_amount");
             * $rsm->addScalarResult("billed_amount", "billed_amount");
             */
            
            // echo $sql;
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param object $entity
     */
    public function postGR($entity)
    {
        $sql_update_gr_row = "
UPDATE nmt_procure_gr_row
SET 
nmt_procure_gr_row.doc_status = '%s',
nmt_procure_gr_row.is_posted=1,
nmt_procure_gr_row.is_draft=0
WHERE nmt_procure_gr_row.gr_id = %s AND nmt_procure_gr_row.is_active =1
";
        
        $sql_gr_row_id = "
SELECT
    fin_vendor_invoice_row.id
FROM fin_vendor_invoice_row
WHERE fin_vendor_invoice_row.is_active=1 AND fin_vendor_invoice_row.invoice_id=%s
";
        
        $sql_update_stock_gr_row = "
UPDATE nmt_inventory_trx
SET nmt_inventory_trx.doc_status = '%s'
WHERE nmt_inventory_trx.is_active=1 AND nmt_inventory_trx.invoice_row_id IN (%s)
";
        
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            return;
        }
        
        try {
            
            /** @var \Application\Entity\NmtProcureGr $entity ;*/
            
            $status = $entity->getDocStatus();
            $entity_id = $entity->getId();
            
            // update procure_gr_row
            $sql_update_gr_row = sprintf($sql_update_gr_row, $status, $entity_id);
            $this->_em->getConnection()->executeUpdate($sql_update_gr_row);
            
            // $sql_gr_row_id
            $sql_gr_row_id = sprintf($sql_gr_row_id, $entity_id);
            
            // update stock gr_row
            $sql_update_stock_gr_row = sprintf($sql_update_stock_gr_row, $status, $sql_ap_row_id);
            $this->_em->getConnection()->executeUpdate($sql_update_stock_gr_row);
        } catch (NoResultException $e) {
            return;
        }
    }

    /**
     * GR List
     */
    public function getGrList($is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = "SELECT
   nmt_procure_gr.*,
   count(nmt_procure_gr_row.id) as total_row
FROM nmt_procure_gr
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.gr_id = nmt_procure_gr.id
WHERE 1
";
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_gr.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_gr.current_state = '" . $current_state . "'";
        }
        
        $sql = $sql . " GROUP BY nmt_procure_gr_row.gr_id";
        
        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }
        
        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGr', 'nmt_procure_gr');
            // $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            // $rsm->addScalarResult("max_row_number", "max_row_number");
            // $rsm->addScalarResult("net_amount", "net_amount");
            // $rsm->addScalarResult("tax_amount", "tax_amount");
            // $rsm->addScalarResult("gross_amount", "gross_amount");
            // $rsm->addScalarResult("billed_amount", "billed_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    // ============================
    /**
     * GR List
     */
    public function getQOList($is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = "SELECT
   nmt_procure_qo.*,
   count(nmt_procure_qo_row.id) as total_row
FROM nmt_procure_qo
LEFT JOIN nmt_procure_qo_row
ON nmt_procure_qo_row.qo_id = nmt_procure_qo.id
WHERE 1
";
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_qo.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_qo.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_qo.current_state = '" . $current_state . "'";
        }
        
        $sql = $sql . " GROUP BY nmt_procure_qo_row.qo_id";
        
        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }
        
        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQo', 'nmt_procure_qo');
            // $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            // $rsm->addScalarResult("max_row_number", "max_row_number");
            // $rsm->addScalarResult("net_amount", "net_amount");
            // $rsm->addScalarResult("tax_amount", "tax_amount");
            // $rsm->addScalarResult("gross_amount", "gross_amount");
            // $rsm->addScalarResult("billed_amount", "billed_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getQoute($id, $token)
    {
        $sql = "
SELECT
	nmt_procure_qo.*,
	COUNT(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.id) ELSE 0 END) AS active_row,
 	COUNT(CASE WHEN nmt_procure_qo_row.id is not null THEN (nmt_procure_qo_row.id) ELSE 0 END) AS total_row,
  	MAX(nmt_procure_qo_row.row_number) AS max_row_number,    
   	SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.net_amount) ELSE 0 END) AS net_amount,
   	SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.tax_amount) ELSE 0 END) AS tax_amount,
   	SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.gross_amount) ELSE 0 END) AS gross_amount
 FROM nmt_procure_qo

left join nmt_procure_qo_row
ON nmt_procure_qo_row.qo_id = nmt_procure_qo.id
WHERE 1

";
        
        $sql = sprintf($sql . " AND nmt_procure_qo.id = %s AND nmt_procure_qo.token='%s' Group BY nmt_procure_qo_row.qo_id", $id, $token);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQo', 'nmt_procure_qo');
            $rsm->addScalarResult("total_row", "total_row");            
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
             
            // echo $sql;
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

