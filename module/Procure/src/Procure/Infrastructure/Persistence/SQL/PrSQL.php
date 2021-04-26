<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrSQL
{

    const PR_ROW_SQL = "
SELECT
	nmt_procure_pr_row.*,

	nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,
    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,

    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,

    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,

    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,

    last_ap.vendor_name as last_vendor_name,
    last_ap.unit_price as last_unit_price,
    last_ap.currency_iso3 as currency_iso3

FROM nmt_procure_pr_row

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
	GROUP BY nmt_procure_po_row.pr_row_id

)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row

	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY nmt_procure_gr_row.pr_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty

	FROM nmt_procure_pr_row
	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id


left  join
(
	SELECT
	fin_vendor_invoice_row.item_id,
	fin_vendor_invoice_row.unit_price,
	fin_vendor_invoice.vendor_name,
	 fin_vendor_invoice.currency_iso3
	FROM
	fin_vendor_invoice_row

	INNER JOIN
	(
	SELECT
		MAX(fin_vendor_invoice_row.id) AS max_id,
		fin_vendor_invoice_row.item_id AS item_id
		FROM fin_vendor_invoice_row

	INNER JOIN fin_vendor_invoice ON
	fin_vendor_invoice_row.invoice_id = fin_vendor_invoice.id
	WHERE fin_vendor_invoice.doc_status='posted'
	GROUP BY fin_vendor_invoice_row.item_id
	)
	AS last_ap
	ON last_ap.max_id = fin_vendor_invoice_row.id AND fin_vendor_invoice_row.item_id = last_ap.item_id

	LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id

WHERE 1 %s
";

    const PR_ROW_SQL_TOTAL = "
SELECT
	count(*)as total

    nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,
    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,

    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,

    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,

    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,

    last_ap.vendor_name as last_vendor_name,
    last_ap.unit_price as last_unit_price,
    last_ap.currency_iso3 as currency_iso3

FROM nmt_procure_pr_row

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
	GROUP BY nmt_procure_po_row.pr_row_id

)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row

	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY nmt_procure_gr_row.pr_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty

	FROM nmt_procure_pr_row
	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id


left  join
(
	SELECT
	fin_vendor_invoice_row.item_id,
	fin_vendor_invoice_row.unit_price,
	fin_vendor_invoice.vendor_name,
	 fin_vendor_invoice.currency_iso3
	FROM
	fin_vendor_invoice_row

	INNER JOIN
	(
	SELECT
		MAX(fin_vendor_invoice_row.id) AS max_id,
		fin_vendor_invoice_row.item_id AS item_id
		FROM fin_vendor_invoice_row

	INNER JOIN fin_vendor_invoice ON
	fin_vendor_invoice_row.invoice_id = fin_vendor_invoice.id
	WHERE fin_vendor_invoice.doc_status='posted'
	GROUP BY fin_vendor_invoice_row.item_id
	)
	AS last_ap
	ON last_ap.max_id = fin_vendor_invoice_row.id AND fin_vendor_invoice_row.item_id = last_ap.item_id

	LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id

WHERE 1 %s
";

    /**
     *
     * @var string
     */
    const PR_ROW_SQL_1 = "
SELECT
	nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,

    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,

	nmt_procure_pr_row.*,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,

    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,

    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,

    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,

    last_ap.vendor_name as vendor_name,
    last_ap.unit_price as unit_price,
    last_ap.currency_iso3 as currency_iso3

FROM nmt_procure_pr_row

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 AND nmt_procure_pr_row.pr_id= %s
	GROUP BY nmt_procure_po_row.pr_row_id

)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row

	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 AND nmt_procure_pr_row.pr_id= %s
    GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 AND nmt_procure_pr_row.pr_id= %s
    GROUP BY nmt_procure_gr_row.pr_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty
	FROM nmt_procure_pr_row

	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1 AND nmt_procure_pr_row.pr_id= %s
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id

left  join
(
	SELECT
	fin_vendor_invoice_row.item_id,
	fin_vendor_invoice_row.unit_price,
	fin_vendor_invoice.vendor_name,
	 fin_vendor_invoice.currency_iso3
	FROM
	fin_vendor_invoice_row

	INNER JOIN
	(
    	SELECT
    		MAX(fin_vendor_invoice_row.id) AS max_id,
    		fin_vendor_invoice_row.item_id AS item_id
    		FROM fin_vendor_invoice_row

    	INNER JOIN fin_vendor_invoice ON
    	fin_vendor_invoice_row.invoice_id = fin_vendor_invoice.id
    	WHERE fin_vendor_invoice.doc_status='posted'
    	GROUP BY fin_vendor_invoice_row.item_id
	)
	AS last_ap
	ON last_ap.max_id = fin_vendor_invoice_row.id AND fin_vendor_invoice_row.item_id = last_ap.item_id

	LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id

WHERE 1 %s
";

    const PR_SQL = "
SELECT
	nmt_procure_pr.id ,
    nmt_procure_pr.pr_number,
    nmt_procure_pr.created_on,
    nmt_procure_pr.lastchange_on,
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

    const PR_ROW_ALL = "
   SELECT
    nmt_procure_pr_row.pr_id AS pr_id,
	nmt_procure_pr_row.id AS pr_row_id,
  	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,

    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty
FROM nmt_procure_pr_row
LEFT JOIN
(
   SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_draft = 1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
	GROUP BY nmt_procure_po_row.pr_row_id

)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
LEFT JOIN
(
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_draft =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row

	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY fin_vendor_invoice_row.pr_row_id

)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
LEFT JOIN
(
    SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_draft =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row

	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY nmt_procure_gr_row.pr_row_id

)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
LEFT JOIN
(
   SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_draft= 1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty

	FROM nmt_procure_pr_row
	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
WHERE 1
";
}
