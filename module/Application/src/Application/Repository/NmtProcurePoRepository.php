<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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
            $sql = $sql . " AND nmt_procure_po.current_state = '".$current_state."'";
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
    
    
   
    public function getPoOf($vendor_id,$is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if($vendor_id > 0){
            $sql = $sql . " AND nmt_procure_po.vendor_id =" . $vendor_id ;
        }else{
            return null;
        }
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_po.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND v.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND nmt_procure_po.current_state = '".$current_state."'";
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
        $sql = 
"
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
        
        //$sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = $sql.  sprintf(" AND nmt_inventory_item.id =%s AND nmt_inventory_item.token='%s'", $item_id, $token);
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
  
}

