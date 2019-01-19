<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author nmt
 *        
 */
class NmtProcurePrRowRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtProcurePrRow $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePrRowRepository")
    private $sql = "
SELECT
	nmt_procure_pr_row.*,
    nmt_inventory_item.item_name,
    nmt_inventory_item.item_sku,
	nmt_inventory_item.checksum AS item_checksum,
	nmt_inventory_item.token AS item_token,
    nmt_inventory_item_picture.picture_id,	
 
    nmt_procure_pr.checksum AS pr_checksum,
	nmt_procure_pr.token AS pr_token,
	nmt_procure_pr.pr_number,
	nmt_procure_pr.pr_auto_number,
    nmt_procure_pr.submitted_on,
    
    ifnull(nmt_inventory_trx_last.vendor_name,nmt_inventory_item_purchasing.vendor_name) as vendor_name,
	ifnull(nmt_inventory_trx_last.vendor_id,nmt_inventory_item_purchasing.vendor_id) as vendor_id,
    ifnull(nmt_inventory_trx_last.vendor_token,nmt_inventory_item_purchasing.vendor_token) as vendor_token,
	ifnull(nmt_inventory_trx_last.vendor_checksum,nmt_inventory_item_purchasing.vendor_checksum) as vendor_checksum,
    
 	ifnull( nmt_inventory_trx_last.vendor_unit_price, nmt_inventory_item_purchasing.vendor_unit_price) as vendor_unit_price,
  	ifnull( nmt_inventory_trx_last.currency, nmt_inventory_item_purchasing.currency) as currency,
 	ifnull( nmt_inventory_trx_last.vendor_item_unit, nmt_inventory_item_purchasing.vendor_item_unit) as vendor_item_unit,
    
    ifnull(nmt_inventory_trx.total_received,0) as total_received,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0
    ,(nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))
    ,0) AS confirmed_balance,
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>=0
    ,0,(nmt_procure_pr_row.quantity*-1 + IFNULL(nmt_inventory_trx.total_received,0))) AS confirmed_free_balance,

	ifnull(fin_vendor_invoice_row.processing_quantity,0) as processing_quantity 
    
FROM nmt_procure_pr_row

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

left join
(
	SELECT nmt_inventory_item_picture.item_id, MAX(nmt_inventory_item_picture.id) AS picture_id  FROM nmt_inventory_item_picture
		WHERE nmt_inventory_item_picture.is_active=1 
	GROUP BY nmt_inventory_item_picture.item_id
)
as nmt_inventory_item_picture
on nmt_inventory_item_picture.item_id = nmt_inventory_item.id

left JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN
(
	SELECT
		nmt_inventory_trx.pr_row_id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received
	FROM nmt_inventory_trx
    WHERE nmt_inventory_trx.is_active =1
	GROUP BY nmt_inventory_trx.pr_row_id
) 
AS nmt_inventory_trx
ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id

LEFT JOIN
(
	SELECT
		fin_vendor_invoice_row.pr_row_id,
		SUM(fin_vendor_invoice_row.quantity) AS processing_quantity
	FROM fin_vendor_invoice_row
	WHERE fin_vendor_invoice_row.current_state!='finalInvoice' AND fin_vendor_invoice_row.is_active=1
	GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
	nmt_bp_vendor.vendor_name,
	nmt_bp_vendor.token as vendor_token,
    nmt_bp_vendor.checksum as vendor_checksum, 
	nmt_application_currency.currency,
	nmt_inventory_trx.*,
	COUNT(nmt_inventory_trx.item_id) AS total_trx,
	MAX(nmt_inventory_trx.trx_date) AS last_trx
	FROM nmt_inventory_trx

	LEFT JOIN nmt_bp_vendor
	ON nmt_bp_vendor.id = nmt_inventory_trx.vendor_id

	LEFT JOIN nmt_application_currency
	ON nmt_application_currency.id = nmt_inventory_trx.currency_id
	WHERE nmt_inventory_trx.is_active=1
    GROUP BY nmt_inventory_trx.item_id
    ORDER BY nmt_inventory_trx.trx_date DESC
	
)
AS nmt_inventory_trx_last
ON nmt_inventory_trx_last.item_id = nmt_procure_pr_row.item_id

LEFT JOIN
(

	SELECT
	nmt_bp_vendor.vendor_name,
    nmt_bp_vendor.token as vendor_token,
    nmt_bp_vendor.checksum as vendor_checksum,
    
	nmt_application_currency.currency,
	nmt_inventory_item_purchasing.*,
	COUNT(nmt_inventory_item_purchasing.item_id) AS total_purchase,
	MAX(nmt_inventory_item_purchasing.created_on) AS last_purchase
	FROM nmt_inventory_item_purchasing

	LEFT JOIN nmt_bp_vendor
	ON nmt_bp_vendor.id = nmt_inventory_item_purchasing.vendor_id
	
 	LEFT JOIN nmt_application_currency
	ON nmt_application_currency.id = nmt_inventory_item_purchasing.currency_id

	WHERE nmt_inventory_item_purchasing.is_active=1
	GROUP BY nmt_inventory_item_purchasing.item_id
)
AS nmt_inventory_item_purchasing
ON nmt_inventory_item_purchasing.item_id = nmt_procure_pr_row.item_id
WHERE 1 
	";

    private $sql1 = "
SELECT
	nmt_procure_pr.id ,
    nmt_procure_pr.pr_number,
    nmt_procure_pr.created_on,
    nmt_procure_pr.last_change_on,
    nmt_procure_pr.submitted_on,
    nmt_procure_pr.is_active,
    nmt_procure_pr.is_draft,
	nmt_procure_pr.pr_auto_number,
    nmt_procure_pr.total_row_manual,
 
    
    nmt_procure_pr.checksum as pr_checksum,
	nmt_procure_pr.token as pr_token,
	year(nmt_procure_pr.created_on) as pr_year,
	month(nmt_procure_pr.created_on) as pr_month,
    ifnull(nmt_procure_pr_row.total_row, 0) as total_row,
    ifnull(nmt_procure_pr_row.row_completed, 0) as row_completed,
    ifnull(nmt_procure_pr_row.row_completed_converted, 0) as row_completed_converted,
    
    ifnull(nmt_procure_pr_row.row_pending, 0) as row_pending,
    
    ifnull(nmt_procure_pr_row.percentage_completed, 0) as percentage_completed,
    ifnull(nmt_procure_pr_row.percentage_completed_converted, 0) as percentage_completed_converted
    
    
FROM nmt_procure_pr

Left JOIN
(
	SELECT
	nmt_procure_pr_row.pr_id,
   	Count(nmt_procure_pr_row.id) as total_row,
	sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))<=0 THEN  1 ELSE 0 END) AS row_completed,
    sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received_converted,0))<=0 THEN  1 ELSE 0 END) AS row_completed_converted,
	sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0 THEN  1 ELSE 0 END) AS row_pending,
	(sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))<=0 THEN  1 ELSE 0 END)/Count(nmt_procure_pr_row.id)) as percentage_completed,
    (sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received_converted,0))<=0 THEN  1 ELSE 0 END)/Count(nmt_procure_pr_row.id)) as percentage_completed_converted

	from nmt_procure_pr_row
	LEFT JOIN
	(
		SELECT
			nmt_inventory_trx.pr_row_id AS pr_row_id,
			SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received,
			SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity*nmt_inventory_trx.conversion_factor ELSE 0 END) AS total_received_converted

		FROM nmt_inventory_trx
        WHERE nmt_inventory_trx.is_active =1
		GROUP BY nmt_inventory_trx.pr_row_id
	) 
	AS nmt_inventory_trx
	ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id
    Where nmt_procure_pr_row.is_active=1
	Group by nmt_procure_pr_row.pr_id
) 
AS nmt_procure_pr_row
ON nmt_procure_pr_row.pr_id = nmt_procure_pr.id

where 1

";

    private $sql_project_item = "
SELECT
nmt_procure_pr_row.id as pr_row_id,
nmt_procure_pr_row.checksum AS pr_row_checksum,
nmt_procure_pr_row.token AS pr_row_token,

nmt_procure_pr_row.pr_id,
nmt_procure_pr.pr_number,
nmt_procure_pr.checksum AS pr_checksum,
nmt_procure_pr.token AS pr_token,
nmt_procure_pr_row.item_id,
nmt_inventory_item.item_name,
nmt_inventory_item.item_sku,
nmt_inventory_item.checksum AS item_checksum,
nmt_inventory_item.token AS item_token,
nmt_inventory_trx.quantity,
nmt_inventory_trx.vendor_unit_price,
nmt_inventory_trx.quantity*vendor_unit_price AS total_price,
nmt_application_currency.currency,
nmt_procure_pr_row.remarks,
nmt_procure_pr_row.fa_remarks,
nmt_inventory_trx.vendor_id,
nmt_bp_vendor.vendor_name,
nmt_bp_vendor.checksum as vendor_checksum,
nmt_bp_vendor.token as vendor_token
FROM nmt_procure_pr_row
LEFT JOIN nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = nmt_inventory_trx.currency_id

LEFT JOIN nmt_bp_vendor
ON nmt_bp_vendor.id = nmt_inventory_trx.vendor_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
WHERE 1

";

    private $sql_get_pr = "
SELECT 
	nmt_procure_pr.*,
	COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.row_number) ELSE null END),0) AS max_row_number,
	COUNT(nmt_procure_pr_row.id) AS total_row
FROM nmt_procure_pr
LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
WHERE 1";

    private $sql2 = "
SELECT
    IFNULL(nmt_inventory_trx.total_received,0) AS total_received,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0,(nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))
    ,0) AS confirmed_balance,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>=0
    ,0,(nmt_procure_pr_row.quantity*-1 + IFNULL(nmt_inventory_trx.total_received,0))) AS confirmed_free_balance,
    
   	IFNULL(nmt_procure_po_row.po_quantity_draft,0) AS po_quantity_draft,
	IFNULL(nmt_procure_po_row.po_quantity_final,0) AS po_quantity_final,
 
	IFNULL(fin_vendor_invoice_row.ap_quantity_final,0) AS ap_quantity_final,
	IFNULL(fin_vendor_invoice_row.ap_quantity_draft,0) AS ap_quantity_draft,
	
    IFNULL(nmt_bp_vendor_last.vendor_name,nmt_bp_vendor_last_purchasing.vendor_name) AS last_vendor_name,
    IFNULL(nmt_inventory_trx_last.vendor_item_unit,nmt_inventory_item_purchasing.vendor_item_unit) AS last_unit,
    IFNULL(nmt_inventory_trx_last.vendor_unit_price,nmt_inventory_item_purchasing.vendor_unit_price) AS last_vendor_unit_price,
	IFNULL(nmt_application_currency_last.currency,nmt_application_currency_last_purchasing.currency) AS last_currency,
	nmt_procure_pr_row.*
        
FROM nmt_procure_pr_row

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN
(
	SELECT
		nmt_inventory_trx.pr_row_id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received
	FROM nmt_inventory_trx
    WHERE nmt_inventory_trx.is_active =1
    GROUP BY nmt_inventory_trx.pr_row_id
) 
AS nmt_inventory_trx
ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id


LEFT JOIN
(
	SELECT
		fin_vendor_invoice_row.pr_row_id,
		SUM(CASE WHEN fin_vendor_invoice_row.current_state='finalInvoice' THEN  fin_vendor_invoice_row.quantity ELSE 0 END)  AS ap_quantity_final,
    	SUM(CASE WHEN fin_vendor_invoice_row.current_state!='finalInvoice' THEN  fin_vendor_invoice_row.quantity ELSE 0 END)  AS ap_quantity_draft
        
	FROM fin_vendor_invoice_row
	WHERE fin_vendor_invoice_row.is_active=1
	GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
    SELECT
		nmt_procure_po_row.pr_row_id,
        nmt_procure_po_row.po_id,
		SUM(CASE WHEN nmt_procure_po_row.current_state='finalPo' THEN  nmt_procure_po_row.quantity ELSE 0 END)  AS po_quantity_final,
    	SUM(CASE WHEN nmt_procure_po_row.current_state!='finalPo' THEN  nmt_procure_po_row.quantity ELSE 0 END)  AS po_quantity_draft
    
  	FROM nmt_procure_po_row
    
   WHERE nmt_procure_po_row.is_active=1
	GROUP BY nmt_procure_po_row.pr_row_id
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

/* Last GR */
LEFT JOIN nmt_inventory_trx AS nmt_inventory_trx_last
ON nmt_inventory_trx_last.id= nmt_inventory_item.last_trx_row

LEFT JOIN nmt_bp_vendor AS nmt_bp_vendor_last
ON nmt_bp_vendor_last.id = nmt_inventory_trx_last.vendor_id

LEFT JOIN nmt_application_currency AS nmt_application_currency_last
ON nmt_application_currency_last.id = nmt_inventory_trx_last.currency_id

/* Last Purchasing */
LEFT JOIN nmt_inventory_item_purchasing
ON nmt_inventory_item_purchasing.id = nmt_inventory_item.last_purchasing

LEFT JOIN nmt_bp_vendor AS nmt_bp_vendor_last_purchasing
ON nmt_bp_vendor_last_purchasing.id = nmt_inventory_item_purchasing.vendor_id

LEFT JOIN nmt_application_currency AS nmt_application_currency_last_purchasing
ON nmt_application_currency_last_purchasing.id = nmt_inventory_item_purchasing.currency_id

WHERE 1
";

    private $sql3 = "
SELECT
    IFNULL(nmt_inventory_trx.total_received,0) AS total_received,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0,(nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))
    ,0) AS confirmed_balance,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>=0
    ,0,(nmt_procure_pr_row.quantity*-1 + IFNULL(nmt_inventory_trx.total_received,0))) AS confirmed_free_balance,
    
   	IFNULL(nmt_procure_po_row.po_quantity_draft,0) AS po_quantity_draft,
	IFNULL(nmt_procure_po_row.po_quantity_final,0) AS po_quantity_final,
 
	IFNULL(fin_vendor_invoice_row.ap_quantity_final,0) AS ap_quantity_final,
	IFNULL(fin_vendor_invoice_row.ap_quantity_draft,0) AS ap_quantity_draft,
	
    IFNULL(nmt_bp_vendor_last.vendor_name,nmt_bp_vendor_last_purchasing.vendor_name) AS last_vendor_name,
    IFNULL(nmt_inventory_trx_last.vendor_item_unit,nmt_inventory_item_purchasing.vendor_item_unit) AS last_unit,
    IFNULL(nmt_inventory_trx_last.vendor_unit_price,nmt_inventory_item_purchasing.vendor_unit_price) AS last_vendor_unit_price,
	IFNULL(nmt_application_currency_last.currency,nmt_application_currency_last_purchasing.currency) AS last_currency,
	nmt_procure_pr_row.*
        
FROM nmt_procure_pr_row

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN
(
	SELECT
		nmt_inventory_trx.pr_row_id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received
	FROM nmt_inventory_trx
    
	LEFT JOIN nmt_procure_pr_row
    ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id
    
    WHERE 1 AND nmt_inventory_trx.is_active =1 AND nmt_procure_pr_row.pr_id=%s
    GROUP BY nmt_inventory_trx.pr_row_id
) 
AS nmt_inventory_trx
ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id


LEFT JOIN
(
	SELECT
		fin_vendor_invoice_row.pr_row_id,
		SUM(CASE WHEN fin_vendor_invoice_row.current_state='finalInvoice' THEN  fin_vendor_invoice_row.quantity ELSE 0 END)  AS ap_quantity_final,
    	SUM(CASE WHEN fin_vendor_invoice_row.current_state!='finalInvoice' THEN  fin_vendor_invoice_row.quantity ELSE 0 END)  AS ap_quantity_draft
        
	FROM fin_vendor_invoice_row
    
    LEFT JOIN nmt_procure_pr_row
    ON nmt_procure_pr_row.id = fin_vendor_invoice_row.pr_row_id
  
 	WHERE fin_vendor_invoice_row.is_active=1 AND nmt_procure_pr_row.pr_id=%s
	GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
    SELECT
		nmt_procure_po_row.pr_row_id,
        nmt_procure_po_row.po_id,
		SUM(CASE WHEN nmt_procure_po_row.current_state='finalPo' THEN  nmt_procure_po_row.quantity ELSE 0 END)  AS po_quantity_final,
    	SUM(CASE WHEN nmt_procure_po_row.current_state!='finalPo' THEN  nmt_procure_po_row.quantity ELSE 0 END)  AS po_quantity_draft
    
  	FROM nmt_procure_po_row
    
      LEFT JOIN nmt_procure_pr_row
    ON nmt_procure_pr_row.id = nmt_procure_po_row.pr_row_id
  
 	WHERE nmt_procure_po_row.is_active=1 AND nmt_procure_pr_row.pr_id=%s

  	GROUP BY nmt_procure_po_row.pr_row_id
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

/* Last GR */
LEFT JOIN nmt_inventory_trx AS nmt_inventory_trx_last
ON nmt_inventory_trx_last.id= nmt_inventory_item.last_trx_row

LEFT JOIN nmt_bp_vendor AS nmt_bp_vendor_last
ON nmt_bp_vendor_last.id = nmt_inventory_trx_last.vendor_id

LEFT JOIN nmt_application_currency AS nmt_application_currency_last
ON nmt_application_currency_last.id = nmt_inventory_trx_last.currency_id

/* Last Purchasing */
LEFT JOIN nmt_inventory_item_purchasing
ON nmt_inventory_item_purchasing.id = nmt_inventory_item.last_purchasing

LEFT JOIN nmt_bp_vendor AS nmt_bp_vendor_last_purchasing
ON nmt_bp_vendor_last_purchasing.id = nmt_inventory_item_purchasing.vendor_id

LEFT JOIN nmt_application_currency AS nmt_application_currency_last_purchasing
ON nmt_application_currency_last_purchasing.id = nmt_inventory_item_purchasing.currency_id
WHERE 1
 
";

    /**
     *
     * @param string $pr_id
     * @param string $token
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     *
     */
    public function getPR($pr_id, $token)
    {
        $sql = $this->sql_get_pr;

        $sql = $sql . " AND nmt_procure_pr.id =" . $pr_id . " AND nmt_procure_pr.token='" . $token . "' GROUP BY nmt_procure_pr.id";

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
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
     * @param string $item_token
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     *
     */
    public function getPrNew($item_id = null, $item_token = null)
    {
        // PR ROW
        $join1_tmp = "
JOIN
(
SELECT 
	COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.row_number) ELSE null END),0) AS max_row_number,
	COUNT(nmt_procure_pr_row.id) AS total_row,
   	nmt_procure_pr.id as pr_id
FROM nmt_procure_pr

LEFT JOIN nmt_procure_pr_row
	ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

WHERE nmt_procure_pr.id = %s and nmt_procure_pr.token='%s'
)
AS nmt_procure_pr_row
ON nmt_procure_pr.id=nmt_procure_pr_row.pr_id ";

        $join1 = sprintf($join1_tmp, $item_id, $item_token);

        // Attachment
        $join2_tmp = "
JOIN
(
SELECT 
   	COUNT(CASE WHEN nmt_application_attachment.is_active =1 THEN (nmt_application_attachment.id) ELSE NULL END) AS total_attachment,
 	COUNT(CASE WHEN (nmt_application_attachment.is_picture =1 AND nmt_application_attachment.is_active =1) THEN (nmt_application_attachment.id) ELSE NULL END) AS total_picture,
	nmt_procure_pr.id as pr_id
FROM nmt_procure_pr
LEFT JOIN nmt_application_attachment
	ON nmt_procure_pr.id = nmt_application_attachment.pr_id
WHERE nmt_procure_pr.id = %s and nmt_procure_pr.token='%s'
)
AS nmt_application_attachment
ON nmt_procure_pr.id=nmt_application_attachment.pr_id ";

        $join2 = sprintf($join2_tmp, $item_id, $item_token);

        $sql = "
SELECT
	nmt_procure_pr.*,
	nmt_application_attachment.total_attachment,
	nmt_application_attachment.total_picture,
	nmt_procure_pr_row.active_row,
	nmt_procure_pr_row.total_row,
	nmt_procure_pr_row.max_row_number
FROM nmt_procure_pr ";

        $sql = $sql . $join1 . $join2;

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePr', 'nmt_procure_pr');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
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
     * GET recent pr row
     *
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     *
     */
    public function getLastCreatedPrRow($limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT
	nmt_procure_pr_row.*
FROM nmt_procure_pr_row
ORDER BY nmt_procure_pr_row.created_on DESC LIMIT %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getAllPrRow($is_active = 1, $pr_year = 0, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;

        if ($is_active == 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        if ($balance == 0) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
        }
        if ($balance == 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
        }
        if ($balance == - 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
        }

        if ($sort_by == "itemName") {
            $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
        } elseif ($sort_by == "prNumber") {
            $sql = $sql . " ORDER BY nmt_procure_pr.pr_number " . $sort;
        } elseif ($sort_by == "vendorName") {
            $sql = $sql . " ORDER BY ifnull(nmt_inventory_trx_last.vendor_name,nmt_inventory_item_purchasing.vendor_name) " . $sort;
        } elseif ($sort_by == "currency") {
            $sql = $sql . " ORDER BY ifnull( nmt_inventory_trx_last.currency, nmt_inventory_item_purchasing.currency) " . $sort;
        } elseif ($sort_by == "unitPrice") {
            $sql = $sql . " ORDER BY ifnull( nmt_inventory_trx_last.vendor_unit_price, nmt_inventory_item_purchasing.vendor_unit_price) " . $sort;
        } elseif ($sort_by == "balance") {
            $sql = $sql . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getAllPrRow1($is_active = 1, $pr_year = 0, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql2;

        if ($is_active == 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        if ($balance == 0) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
        }
        if ($balance == 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
        }
        if ($balance == - 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
        }

        switch ($sort_by) {
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;

            case "prNumber":
                $sql = $sql . " ORDER BY nmt_procure_pr.pr_number " . $sort;
                break;

            case "vendorName":
                $sql = $sql . " ORDER BY IFNULL(nmt_bp_vendor_last.vendor_name,nmt_bp_vendor_last_purchasing.vendor_name) " . $sort;
                break;

            case "currency":
                $sql = $sql . " ORDER BY IFNULL(nmt_application_currency_last.currency,nmt_application_currency_last_purchasing.currency) " . $sort;
                break;

            case "unitPrice":
                $sql = $sql . " ORDER BY IFNULL(nmt_inventory_trx_last.vendor_unit_price,nmt_inventory_item_purchasing.vendor_unit_price) " . $sort;
                break;

            case "balance":
                $sql = $sql . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');

            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");

            $rsm->addScalarResult("po_quantity_final", "po_quantity_final");
            $rsm->addScalarResult("po_quantity_draft", "po_quantity_draft");
            $rsm->addScalarResult("ap_quantity_final", "ap_quantity_final");
            $rsm->addScalarResult("ap_quantity_draft", "ap_quantity_draft");

            $rsm->addScalarResult("last_vendor_name", "last_vendor_name");
            $rsm->addScalarResult("last_vendor_unit_price", "last_vendor_unit_price");
            $rsm->addScalarResult("last_currency", "last_currency");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param string $item_type
     * @param boolean $is_active
     * @param boolean $is_fixed_asset
     * @return mixed
     */
    public function getTotalPrRow1($is_active = 1, $pr_year = 0, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)

    {
        $sql = "SELECT count(*) as total_row FROM nmt_procure_pr_row Where 1 ";

        $sql = $this->sql2;

        if ($is_active == 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        // IMPORTANT
        $sql = $sql . " GROUP BY nmt_procure_pr_row.id";

        if ($balance == 0) {
            $sql = $sql . " HAVING (nmt_procure_pr_row.quantity - SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END)) <= 0";
        }
        if ($balance == 1) {
            $sql = $sql . " HAVING (nmt_procure_pr_row.quantity - SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END)) > 0";
        }
        if ($balance == - 1) {
            $sql = $sql . " HAVING (nmt_procure_pr_row.quantity - SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END)) < 0";
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_row", "total_row");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return (int) $result['total_row'];
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getPrRow($pr_id, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;

        $sql = $sql . " AND nmt_procure_pr_row.is_active=1 AND nmt_procure_pr_row.pr_id =" . $pr_id;

        if ($balance == 0) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
        }
        if ($balance == 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
        }
        if ($balance == - 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
        }

        switch ($sort_by) {
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_pr_row.created_on " . $sort;
                break;
            case "balance":
                $sql = $sql . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
                break;
            case "prSubmitted":
                $sql = $sql . " ORDER BY nmt_procure_pr.submitted_on" . $sort;
                break;
            case "rowNumber":
                $sql = $sql . " ORDER BY nmt_procure_pr_row.row_number " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getPrRow1($pr_id, $pr_token, $is_active = 1, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = sprintf($this->sql3, $pr_id, $pr_id, $pr_id);

        if ($is_active == 1) {
            $sql = $sql . " AND nmt_procure_pr_row.is_active = 1 ";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_procure_pr_row.is_active = 0)";
        }
        
        $sql = $sql . sprintf(" AND nmt_procure_pr.id =%s AND nmt_procure_pr.token ='%s'", $pr_id, $pr_token);
        
        switch ($sort_by) {
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_procure_pr_row.created_on " . $sort;
                break;
            case "balance":
                $sql = $sql . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
                break;
            case "prSubmitted":
                $sql = $sql . " ORDER BY nmt_procure_pr.submitted_on" . $sort;
                break;
            case "rowNumber":
                $sql = $sql . " ORDER BY nmt_procure_pr_row.row_number " . $sort;
                break;
        }

        $sql = $sql . ";";
        // echo $$sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');

            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");

            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("po_quantity_final", "po_quantity_final");
            $rsm->addScalarResult("po_quantity_draft", "po_quantity_draft");
            $rsm->addScalarResult("ap_quantity_final", "ap_quantity_final");
            $rsm->addScalarResult("ap_quantity_draft", "ap_quantity_draft");

            $rsm->addScalarResult("last_vendor_name", "last_vendor_name");
            $rsm->addScalarResult("last_vendor_unit_price", "last_vendor_unit_price");
            $rsm->addScalarResult("last_currency", "last_currency");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getPrList($row_number = 1, $pr_year = 0, $is_active = null, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql1;

        if ($pr_year > 0) {
            $sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

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

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param int $project_id
     * @return array
     */
    public function getProjectItem($project_id)
    {
        $sql = $this->sql_project_item;

        $sql = $sql . " AND nmt_procure_pr_row.project_id=" . $project_id;
        $sql = $sql . ";";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
    public function downloadAllPrRows($is_active = 1, $pr_year = 0, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql;

        if ($is_active == 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        if ($balance == 0) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
        }
        if ($balance == 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
        }
        if ($balance == - 1) {
            $sql = $sql . " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
        }

        switch ($sort_by) {
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;
            case "prNumber":
                $sql = $sql . " ORDER BY nmt_procure_pr.pr_number " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY ifnull(nmt_inventory_trx_last.vendor_name,nmt_inventory_item_purchasing.vendor_name) " . $sort;
                break;
            case "currency":
                $sql = $sql . " ORDER BY ifnull( nmt_inventory_trx_last.currency, nmt_inventory_item_purchasing.currency) " . $sort;
                break;
            case "unitPrice":
                $sql = $sql . " ORDER BY ifnull( nmt_inventory_trx_last.vendor_unit_price, nmt_inventory_item_purchasing.vendor_unit_price) " . $sort;
                break;
            case "balance":
                $sql = $sql . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("item_sku", "item_sku");
            $rsm->addScalarResult("pr_number", "pr_number");
            $rsm->addScalarResult("submitted_on", "submitted_on");
            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("vendor_id", "vendor_id");
            $rsm->addScalarResult("vendor_token", "vendor_token");
            $rsm->addScalarResult("vendor_checksum", "vendor_checksum");
            $rsm->addScalarResult("currency", "currency");
            $rsm->addScalarResult("vendor_item_unit", "vendor_item_unit");
            $rsm->addScalarResult("vendor_unit_price", "vendor_unit_price");
            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            $rsm->addScalarResult("processing_quantity", "processing_quantity");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @return array|NULL
     */
    public function downloadPrRows($pr_id, $token)
    {
        $sql = $this->sql;

        $sql = $sql . " AND nmt_procure_pr.id =" . $pr_id . " AND nmt_procure_pr.token='" . $token . "'";

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("item_sku", "item_sku");
            $rsm->addScalarResult("picture_id", "picture_id");
            $rsm->addScalarResult("pr_number", "pr_number");
            $rsm->addScalarResult("submitted_on", "submitted_on");
            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("vendor_id", "vendor_id");
            $rsm->addScalarResult("vendor_token", "vendor_token");
            $rsm->addScalarResult("vendor_checksum", "vendor_checksum");
            $rsm->addScalarResult("currency", "currency");
            $rsm->addScalarResult("vendor_item_unit", "vendor_item_unit");
            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            $rsm->addScalarResult("processing_quantity", "processing_quantity");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @return array|NULL
     */
    public function getPrRowV1($pr_id, $token)
    {
        $sql = $this->sql;

        $sql = $sql . " AND nmt_procure_pr.id =" . $pr_id . " AND nmt_procure_pr.token='" . $token . "'";

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            // $rsm->addScalarResult("item_name", "item_name");
            // $rsm->addScalarResult("item_sku", "item_sku");
            // $rsm->addScalarResult("pr_number", "pr_number");
            // $rsm->addScalarResult("submitted_on", "submitted_on");
            // $rsm->addScalarResult("vendor_name", "vendor_name");
            // $rsm->addScalarResult("vendor_id", "vendor_id");
            // $rsm->addScalarResult("vendor_token", "vendor_token");
            // $rsm->addScalarResult("vendor_checksum", "vendor_checksum");
            // $rsm->addScalarResult("currency", "currency");
            // $rsm->addScalarResult("vendor_item_unit", "vendor_item_unit");
            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            $rsm->addScalarResult("processing_quantity", "processing_quantity");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @return array|NULL
     */
    public function getPrOfItem($item_id, $token)
    {
        $sql = $this->sql;

        $sql = $sql . " AND nmt_inventory_item.id =" . $item_id . " AND nmt_inventory_item.token='" . $token . "'";
        $sql = $sql . " ORDER BY nmt_procure_pr.submitted_on DESC ";

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("item_sku", "item_sku");
            $rsm->addScalarResult("pr_number", "pr_number");
            $rsm->addScalarResult("submitted_on", "submitted_on");
            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("vendor_id", "vendor_id");
            $rsm->addScalarResult("vendor_token", "vendor_token");
            $rsm->addScalarResult("vendor_checksum", "vendor_checksum");
            $rsm->addScalarResult("currency", "currency");
            $rsm->addScalarResult("vendor_item_unit", "vendor_item_unit");
            $rsm->addScalarResult("total_received", "total_received");
            $rsm->addScalarResult("confirmed_balance", "confirmed_balance");
            $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            $rsm->addScalarResult("processing_quantity", "processing_quantity");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    //

    /**
     *
     * @return array|NULL
     */
    public function getPrOfItem1($item_id, $token)
    {
        $sql = \Application\Repository\SQL\NmtProcurePrRowRepositorySQL::PR_ROW_SQL;
        $sql_tmp = ' AND nmt_procure_pr_row.item_id=' . $item_id;

        $sql = sprintf($sql, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp);
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("pr_qty", "pr_qty");
            
            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");

            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            
            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");
        
            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     *
     * @return array|NULL
     */
    public function getPrRow2($pr_id, $token)
    {
        $sql = \Application\Repository\SQL\NmtProcurePrRowRepositorySQL::PR_ROW_SQL;
        $sql_tmp = ' AND nmt_procure_pr_row.pr_id=' . $pr_id;
        
        $sql = sprintf($sql, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp);
        // echo $sql;
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("pr_qty", "pr_qty");
            
            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");
            
            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");
            
            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");
            
            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    
}

