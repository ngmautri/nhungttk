<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author nmt
 *        
 */
class FinVendorInvoiceRepository extends EntityRepository
{

    /** @var \Application\Entity\FinVendorInvoice $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\FinVendorInvoiceRepository")
    private $sql = "
SELECT
	fin_vendor_invoice.*,
	COUNT(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.row_number) ELSE null END),0) AS max_row_number,
	COUNT(fin_vendor_invoice_row.id) AS total_row,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) AS gross_amount
FROM fin_vendor_invoice
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
WHERE 1";
    
    private $sql_tmp = "
SELECT
	fin_vendor_invoice.*,
	COUNT(CASE WHEN fin_vendor_invoice_row_tmp.is_active =1 THEN (fin_vendor_invoice_row_tmp.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN fin_vendor_invoice_row_tmp.is_active =1 THEN (fin_vendor_invoice_row_tmp.row_number) ELSE null END),0) AS max_row_number,
	COUNT(fin_vendor_invoice_row_tmp.id) AS total_row,
	SUM(CASE WHEN fin_vendor_invoice_row_tmp.is_active =1 THEN (fin_vendor_invoice_row_tmp.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN fin_vendor_invoice_row_tmp.is_active =1 THEN (fin_vendor_invoice_row_tmp.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN fin_vendor_invoice_row_tmp.is_active =1 THEN (fin_vendor_invoice_row_tmp.gross_amount) ELSE 0 END) AS gross_amount
FROM fin_vendor_invoice
LEFT JOIN fin_vendor_invoice_row_tmp
ON fin_vendor_invoice.id = fin_vendor_invoice_row_tmp.invoice_id
WHERE 1";

    /**
     *
     * @param unknown $invoice_id
     * @param unknown $token
     * @param unknown $filter_by
     * @param unknown $sort_by
     * @param unknown $sort
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     */
    public function getVendorInvoice($invoice_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = $this->sql;
        
        $sql = $sql . " AND fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='" . $token . "' GROUP BY fin_vendor_invoice.id";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     *
     * @param unknown $invoice_id
     * @param unknown $token
     * @param unknown $filter_by
     * @param unknown $sort_by
     * @param unknown $sort
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     */
    public function getVendorInvoiceTmp($invoice_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = $this->sql_tmp;
        
        $sql = $sql . " AND fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='" . $token . "' GROUP BY fin_vendor_invoice.id";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param number $is_active
     * @param unknown $current_state
     * @param unknown $filter_by
     * @param unknown $sort_by
     * @param unknown $sort
     * @param number $limit
     * @param number $offset
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     */
    public function getVendorInvoiceList($is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if ($is_active == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '".$current_state."'";
        }
        
        $sql = $sql . " GROUP BY fin_vendor_invoice.id";
        
        switch ($sort_by) {
            case "invoiceDate":
                $sql = $sql . " ORDER BY fin_vendor_invoice.invoice_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY fin_vendor_invoice.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY fin_vendor_invoice.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY fin_vendor_invoice.currency_iso3 " . $sort;
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    
    /**
     * 
     * @param unknown $vendor_id
     * @param unknown $token
     * @param number $is_active
     * @param unknown $current_state
     * @param unknown $filter_by
     * @param unknown $sort_by
     * @param unknown $sort
     * @param number $limit
     * @param number $offset
     * @return array|NULL
     */
    public function getInvoicesOf($vendor_id,$is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if($vendor_id > 0){
            $sql = $sql . " AND fin_vendor_invoice.vendor_id =" . $vendor_id ;
        }else{
            return null;
        }
        
        if ($is_active == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '".$current_state."'";
        }
        
        $sql = $sql . " GROUP BY fin_vendor_invoice.id";
        
        switch ($sort_by) {
            case "invoiceDate":
                $sql = $sql . " ORDER BY fin_vendor_invoice.invoice_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY fin_vendor_invoice.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY fin_vendor_invoice.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY fin_vendor_invoice.currency_iso3 " . $sort;
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    
   /**
    * 
    * @param unknown $invoice_id
    * @param unknown $token
    * @param unknown $filter_by
    * @param unknown $sort_by
    * @param unknown $sort
    * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
    */
    public function downLoadVendorInvoice($invoice_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = 
"
SELECT 
fin_vendor_invoice_row.*
FROM fin_vendor_invoice_row
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
WHERE 1
";
        
        $sql = $sql . " AND fin_vendor_invoice_row.is_active=1 AND fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='" . $token . "'";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

