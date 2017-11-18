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
class NmtProcurePoRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtProcurePo $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePoRepository")
    private $sql = "
SELECT
	nmt_procure_po.*,
	COUNT(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.row_number) ELSE null END),0) AS max_row_number,
	COUNT(nmt_procure_po_row.id) AS total_row,
	SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.gross_amount) ELSE 0 END) AS gross_amount
FROM nmt_procure_po
LEFT JOIN nmt_procure_po_row
ON nmt_procure_po.id = nmt_procure_po_row.po_id
WHERE 1";

  /**
   * 
   * @param unknown $po_id
   * @param unknown $token
   * @param unknown $filter_by
   * @param unknown $sort_by
   * @param unknown $sort
   * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
   */
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
     * @return unknown|NULL
     */
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
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    
    
    /**
     * 
     * @param unknown $po_id
     * @param unknown $token
     * @param unknown $filter_by
     * @param unknown $sort_by
     * @param unknown $sort
     * @return array|NULL
     */
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
}

