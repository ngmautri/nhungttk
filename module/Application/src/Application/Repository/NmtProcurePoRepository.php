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

    public function getPo($po_id, $token = null, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = $this->sql;

        if($token!==null){
            $sql = $sql . " AND nmt_procure_po.id =" . $po_id . " AND nmt_procure_po.token='" . $token . "' GROUP BY nmt_procure_po.id";
        }else{
            $sql = $sql . " AND nmt_procure_po.id =" . $po_id . " GROUP BY nmt_procure_po.id";
            
        }
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

    public function getPoList($is_active = 1, $current_state = null,$docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
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

    /**
     * 
     * @param int $item_id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
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
     * 
     * @param number $vendor_id
     * @param string $token
     * @param static $sort_by
     * @param string $order_by
     * @param string $sort
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getPoRowOfVendor($vendor_id=null, $vendor_token=null, $sort_by=null, $order='DESC', $limit=0, $offset=0)
    {
        $sql = "
SELECT
	nmt_procure_po_row.*,
    nmt_inventory_item.item_name,
    nmt_procure_po.vendor_id,
    nmt_procure_po.vendor_name,
    nmt_procure_po.currency_id,
    nmt_procure_po.contract_no,
    nmt_procure_po.contract_date
	
FROM nmt_procure_po_row
            
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id
WHERE 1            
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = $sql . sprintf(" AND nmt_procure_po.vendor_id=%s", $vendor_id);
    
        switch ($sort_by) {
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_po_row.created_on " . $order;
                break;
            case "transactionStatus":
                $sql = $sql . " ORDER BY nmt_procure_po_row.transaction_status " . $order;
                break;                
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $order;
                break;
            case "poNumber":
                $sql = $sql . " ORDER BY nmt_procure_po.contract_no " . $order;
                break;
            case "poDate":
                $sql = $sql . " ORDER BY nmt_procure_po.contract_date " . $order;
                break;
            case "rowQuantity":
                $sql = $sql . " ORDER BY nmt_procure_po_row.doc_quantity " . $order;
                break;
            case "rowUnitPrice":
                $sql = $sql . " ORDER BY nmt_procure_po_row.doc_unit_price " . $order;
                break;
           }
        
        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }
        
        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        
        //echo $sql;
        
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
     *
     * @param int $id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getPOStatus($id, $token)
    {
        $sql1 = "
SELECT
    nmt_procure_po_row.id AS po_row_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount
      
FROM nmt_procure_po_row

LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id =  nmt_procure_po_row.id

WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

        $sql2 = "
SELECT
     
	IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS draft_gr_qty,
    IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS posted_gr_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_gr_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END)-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS open_gr_qty,
    nmt_procure_po_row.id as po_row_id
    
FROM nmt_procure_po_row

LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id =  nmt_procure_po_row.id

WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1
GROUP BY nmt_procure_po_row.id
";

        $sql = "
SELECT
* 
FROM nmt_procure_po_row

LEFT JOIN 
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id

LEFT JOIN 
(%s)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id

WHERE nmt_procure_po_row.po_id=%s AND nmt_procure_po_row.is_active=1 order by row_number";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $sql1 = sprintf($sql1, $id);
        $sql2 = sprintf($sql2, $id);

        $sql = sprintf($sql, $sql1, $sql2, $id);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
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

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getGRStatus($id, $token)
    {
        $sql1 = "
SELECT
    nmt_procure_gr_row.id AS gr_row_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_gr_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_gr_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount
            
FROM nmt_procure_gr_row
            
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id =  nmt_procure_gr_row.id
            
WHERE nmt_procure_gr_row.gr_id=%s
GROUP BY nmt_procure_gr_row.id
";

        //$sql2 = "";

        $sql = "
SELECT
*
FROM nmt_procure_gr_row
            
LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id = nmt_procure_gr_row.id
            
WHERE nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $sql1 = sprintf($sql1, $id);
        // $sql2 = sprintf($sql2, $id);

        $sql = sprintf($sql, $sql1, $id);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');

            $rsm->addScalarResult("draft_gr_qty", "draft_gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            $rsm->addScalarResult("confirmed_gr_balance", "confirmed_gr_balance");
            $rsm->addScalarResult("open_gr_qty", "open_gr_qty");

            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
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

    /**
     *
     * @param int $id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getOpenPoAP($id, $token)
    {
        $sql = "
SELECT
    fin_vendor_invoice_row.id AS ap_row_id,
    fin_vendor_invoice_row.po_row_id AS po_row_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_qty,
    nmt_procure_po_row.*
FROM nmt_procure_po_row
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id =  nmt_procure_po_row.id
WHERE nmt_procure_po_row.po_id=%s
GROUP BY nmt_procure_po_row.id
";

        $sql = sprintf($sql, $id);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePoRow', 'nmt_procure_po_row');
            $rsm->addScalarResult("draft_qty", "draft_qty");
            $rsm->addScalarResult("posted_qty", "posted_qty");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("open_qty", "open_qty");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return string
     */
    public function updatePo($id, $token = null)
    {
        $message = "";

        try {
            $po_rows = $this->getPOStatus($id, $token);

            if (count($po_rows) > 0) {

                /**@var \Application\Entity\NmtProcurePoRow $po;*/
                $po = null;
                $completed = True;

                foreach ($po_rows as $r) {

                    /**@var \Application\Entity\NmtProcurePoRow $po_row ;*/
                    $po_row = $r[0];

                    if ($po == null) {
                        $po = $po_row->getPo();
                    }

                    if ($po_row->getIsActive() == 0) {
                        $po_row->setTransactionStatus(\Application\Model\Constants::TRANSACTION_STATUS_CLOSED);
                        continue;
                    }

                    // close row
                    if ($r['open_gr_qty'] == 0 AND $r['open_ap_qty'] == 0) {
                        $po_row->setTransactionStatus(\Application\Model\Constants::TRANSACTION_STATUS_COMPLETED);
                    } else {
                        $completed = false;
                        $po_row->setTransactionStatus(\Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED);
                    }

                    $this->_em->persist($po_row);
                }

                if ($completed == true) {
                    $po->setTransactionStatus(\Application\Model\Constants::TRANSACTION_STATUS_COMPLETED);
                } else {
                    $po->setTransactionStatus(\Application\Model\Constants::TRANSACTION_STATUS_UNCOMPLETED);
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
     *
     * @param int $id
     * @return string[]|string[]|NULL[]
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

    /**
     *
     * @param int $id
     * @return string[]|string[]|NULL[]
     */
    public function updatePOofAP($id)
    {
        $sql = "
SELECT
fin_vendor_invoice.*
nmt_procure_po_row.po_id as po_id

FROM fin_vendor_invoice

left join fin_vendor_invoice_row
on fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id

left join nmt_procure_po_row
on nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id

where fin_vendor_invoice.id=%s
group by nmt_procure_po_row.po_id
            
";

        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;

        $sql = sprintf($sql, $id);

        // echo $sql;
        $message = array();
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
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
    Count(nmt_procure_gr_row.id) as total_row,
  	COUNT(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.row_number) ELSE null END),0) AS max_row_number

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
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
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
    public function getGrList($is_active = 1, $current_state = null, $docStatus=null,$filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
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
        
        if ($docStatus != null) {
            $sql = $sql . " AND nmt_procure_gr.doc_status = '" . $docStatus . "'";
        }
      

        $sql = $sql . " GROUP BY nmt_procure_gr.id";
        
        switch ($sort_by) {
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_gr.created_on " . $sort;
                break;
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_procure_gr.sys_number " . $sort;
                break;
     
            case "docStatus":
                $sql = $sql . " ORDER BY nmt_procure_gr.doc_status " . $sort;
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
    public function getQOList($is_active = 1, $current_state = null, $docStatus =null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
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
        
        if ($docStatus != null) {
            $sql = $sql . " AND nmt_procure_qo.doc_status = '" . $docStatus . "'";
        }

        $sql = $sql . " GROUP BY nmt_procure_qo_row.qo_id";

        $sql = $sql . " ORDER BY sys_number DESC";

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

    /**
     *
     * @param int $item_id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getQoOfItem($item_id, $token)
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
     * @param int $item_id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getItemPrice($item_id, $token)
    {
        $sql = \Application\Repository\SQL\NmtProcurePoRepositorySQL::ITEM_PRICE_SQL;
        $sql = sprintf($sql, $item_id, $item_id);
        
        try {
           } catch (NoResultException $e) {
            return null;
        }
    }
    
    
    /**
     *
     * @param int $item_id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getPriceOfItem($item_id, $token)
    {
        $sql = sprintf(\Application\Repository\SQL\NmtProcurePoRepositorySQL::ITEM_PRICE_SQL,$item_id,$item_id,$item_id);
      
         try {
             $stmt = $this->_em->getConnection()->prepare($sql);
             $stmt->execute();
             return $stmt->fetchAll();
        } catch (NoResultException $e) {
            return null;
        }
    }
}

