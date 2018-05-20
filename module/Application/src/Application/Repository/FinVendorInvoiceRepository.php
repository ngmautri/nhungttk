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
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) AS gross_amount,
    
    ifnull(nmt_application_attachment.total_attachment,0) as total_attachment,
    ifnull(nmt_application_attachment.total_picture,0) as total_picture
  
FROM fin_vendor_invoice

LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id

LEFT JOIN
(
SELECT
	fin_vendor_invoice.id as v_invoice_id,
	COUNT(nmt_application_attachment.id) AS total_attachment,
   	COUNT(CASE WHEN nmt_application_attachment.is_picture =1 THEN (nmt_application_attachment.id) ELSE NULL END) AS total_picture
    FROM fin_vendor_invoice
    INNer JOIN nmt_application_attachment
    ON fin_vendor_invoice.id = nmt_application_attachment.v_invoice_id
    group by fin_vendor_invoice.id
)
AS nmt_application_attachment
ON fin_vendor_invoice.id = nmt_application_attachment.v_invoice_id

where 1


";

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
     * @param int $invoice_id
     * @param string $token
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
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
            $rsm->addScalarResult("total_attachment", "total_attachment");
            $rsm->addScalarResult("total_picture", "total_picture");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param int $invoice_id
     * @param string $token
     */
    public function getAPInvoice($invoice_id, $token)
    {
        $sql = $this->sql;
        
        $sql = $sql . " AND fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='" . $token . "' GROUP BY fin_vendor_invoice.id";
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("total_attachment", "total_attachment");
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
     * @param int $invoice_id
     * @param string $token
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
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
     * @param string $current_state
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
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
            $sql = $sql . " AND fin_vendor_invoice.current_state = '" . $current_state . "'";
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
     * @param int $vendor_id
     * @param string $token
     * @param int $is_active
     * @param string $current_state
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
     * @param number $limit
     * @param number $offset
     * @return array|NULL
     */
    public function getInvoicesOf($vendor_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;
        
        if ($vendor_id > 0) {
            $sql = $sql . " AND fin_vendor_invoice.vendor_id =" . $vendor_id;
        } else {
            return null;
        }
        
        if ($is_active == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }
        
        if ($current_state != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '" . $current_state . "'";
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
     * @param number $invoice_id
     * @param string $token
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function downLoadVendorInvoice($invoice_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = "
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

    /**
     *
     * @param number $invoice_id
     * @param string $token
     * @param string $filter_by
     * @param string $sort_by
     * @param string $sort
     * @return array|NULL
     */
    public function getAPInvoiceTmp($invoice_id, $token, $filter_by = null, $sort_by = null, $sort = null)
    {
        $sql = "
SELECT
fin_vendor_invoice_row_tmp.*
FROM fin_vendor_invoice_row_tmp
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row_tmp.invoice_id
WHERE 1
";
        
        $sql = $sql . " AND fin_vendor_invoice_row_tmp.is_active=1 AND fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='" . $token . "'";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRowTmp', 'fin_vendor_invoice_row_tmp');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param number $item_id
     * @param string $token
     * @return array|NULL
     */
    public function getAPOfItem($item_id, $token)
    {
        $sql = "
SELECT
    nmt_inventory_item.item_name as item_name,
	fin_vendor_invoice_row.*
FROM fin_vendor_invoice_row
            
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id
WHERE 1
            
";
        
        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;
        
        $sql = $sql . " AND nmt_inventory_item.id =" . $item_id . " AND nmt_inventory_item.token='" . $token . "'";
        $sql = $sql . " ORDER BY fin_vendor_invoice.invoice_date DESC ";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
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
     * @param number $rate
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT

fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate as lak_unit_price,
fin_vendor_invoice_row.*

FROM fin_vendor_invoice_row

LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id

WHERE fin_vendor_invoice_row.is_active=1
group by fin_vendor_invoice_row.item_id
ORDER BY (fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate) DESC
LIMIT %s
";
        
        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }
        
        $sql = sprintf($sql_tmp,$limit);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $rsm->addScalarResult("lak_unit_price", "lak_unit_price");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

