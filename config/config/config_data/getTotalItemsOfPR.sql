	/* TOTAL PR Items*/
    SELECT
		mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
    FROM
        mla_purchase_request_items
    JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
    GROUP BY mla_purchase_requests.id
 	/* TOTAL PR Items*/
 