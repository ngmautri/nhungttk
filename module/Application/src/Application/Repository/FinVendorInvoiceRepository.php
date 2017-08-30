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

    ";

    public function getVendorInvoice($invoice_id,$token)
    {
        $sql = "
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

WHERE fin_vendor_invoice.id =" . $invoice_id . " AND fin_vendor_invoice.token='". $token ."' GROUP BY fin_vendor_invoice.id";
        
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
     * @todo
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getPrList($row_number = 1, $is_active = null, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql1;
        
        if ($row_number == 1) {
            $sql = $sql . " AND ifnull(nmt_procure_pr_row.total_row, 0) > 0";
        } elseif ($row_number == 0) {
            $sql = $sql . " AND ifnull(nmt_procure_pr_row.total_row, 0) = 0";
        }
        
        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active=  1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_pr.is_active = 0";
        }
        
        // Group
        
        // fullfiled
        if ($balance == 0) {
            $sql = $sql . " AND ifnull(nmt_procure_pr_row.total_row, 0)	<=ifnull(nmt_procure_pr_row.row_completed, 0)";
        } elseif ($balance == 1) {
            $sql = $sql . " AND ifnull(nmt_procure_pr_row.total_row, 0)	> ifnull(nmt_procure_pr_row.row_completed, 0)";
        }
        
        if ($sort_by == "prNumber") {
            $sql = $sql . " ORDER BY nmt_procure_pr.pr_number " . $sort;
        } elseif ($sort_by == "createdOn") {
            $sql = $sql . " ORDER BY nmt_procure_pr.created_on " . $sort;
        } elseif ($sort_by == "completion") {
            $sql = $sql . " ORDER BY ifnull(nmt_procure_pr_row.percentage_completed, 0) " . $sort;
        } elseif ($sort_by == "submittedOn") {
            $sql = $sql . " ORDER BY nmt_procure_pr.submitted_on " . $sort;
        }
        
        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }
        
        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        
        $sql = $sql . ";";
		
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ();
	}
	
	
}

