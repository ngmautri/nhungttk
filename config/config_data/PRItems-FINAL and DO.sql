/* ALL PR ITEMS*/

SELECT
	mla_purchase_request_items.*,    
    mla_purchase_requests.pr_number,
    mla_purchase_requests.auto_pr_number AS pr_auto_number,
    mla_purchase_requests.name AS pr_name,
	mla_purchase_requests.description AS pr_description,
    mla_purchase_requests.requested_by AS pr_requested_by,
    mla_purchase_requests.requested_on AS pr_requested_on,
    
    mla_purchase_requests_workflows.status AS pr_last_status,
	mla_purchase_requests_workflows.updated_on AS pr_last_status_on,
	mla_purchase_requests_workflows.updated_by AS pr_last_status_by,
    
 	YEAR(mla_purchase_requests.requested_on) AS pr_year,
    MONTH(mla_purchase_requests.requested_on) AS pr_month,
    
    mla_users.email AS requester_email,
 	CONCAT (mla_users.firstname,' ',mla_users.lastname ) AS pr_requester_name,
    mla_departments.id AS pr_of_department_id,
	mla_departments.name AS pr_of_department,
 	mla_departments.status AS pr_of_department_status,
    
    IFNULL(mla_delivery_items_workflows.total_received_quantity,0) AS total_received_quantity,
    IFNULL(mla_delivery_items_workflows.unconfirmed_quantity,0) AS unconfirmed_quantity,
	IFNULL(mla_delivery_items_workflows.confirmed_quantity,0) AS confirmed_quantity,
	IFNULL(mla_delivery_items_workflows.rejected_quantity,0) AS rejected_quantity,
	
    IF ((mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))
    ,0) AS confirmed_balance,
	
	 IF ((mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0
	, 0
	,IFNULL(mla_delivery_items_workflows.confirmed_quantity,0)-mla_purchase_request_items.quantity) AS confirmed_free_balance,
 	
    mla_spareparts.tag AS sp_tag,     
	
    mla_delivery_items_workflows_article.dn_item_id AS article_last_dn_item_id,
	mla_delivery_items_workflows_article.status AS article_dn_last_status,   
	mla_delivery_items_workflows_article.updated_by AS article_dn_last_status_by,   
	mla_delivery_items_workflows_article.updated_on AS article_dn_last_status_on,
            
    mla_delivery_items_article.vendor_id AS article_vendor_id,
    mla_delivery_items_article.price AS article_price,
    mla_delivery_items_article.currency AS article_currency,

    mla_vendor_article.name AS article_vendor_name,
    
            
	mla_delivery_items_workflows_sp.dn_item_id AS sp_last_dn_item_id,
	mla_delivery_items_workflows_sp.status AS sp_dn_last_status,   
	mla_delivery_items_workflows_sp.updated_by AS sp_dn_last_status_by,   
	mla_delivery_items_workflows_sp.updated_on AS sp_dn_last_status_on,
    
	mla_delivery_items_sp.vendor_id AS sp_vendor_id,
	mla_vendor_sp.name AS sp_vendor_name,
    mla_delivery_items_sp.price AS sp_price,
    mla_delivery_items_sp.currency AS sp_currency,
	
    mla_po_item.id AS po_item_id,
    mla_po_item.vendor_id AS po_vendor_id,
    mla_po_item.price AS po_price,
     mla_po_item.currency AS po_currency,
    mla_po_item.payment_method AS po_payment_method,
    mla_vendors_po.name AS po_vendor_name,
    
     
    mla_delivery_items.id AS dn_item_id,
    mla_delivery_items.vendor_id AS dn_vendor_id,
    mla_delivery_items.price AS dn_price,
    mla_delivery_items.currency AS dn_currency,
    mla_delivery_items.payment_method AS dn_payment_method,
    mla_vendors_dn.name AS  dn_vendor_name,
    
	mla_delivery_items.delivery_id,
    mla_delivery_items.delivered_quantity,
    mla_delivery_items.last_workflow_id AS dn_item_last_workflow_id,
    
    IFNULL(CASE WHEN mla_delivery_items_workflows_received.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END,0) AS dn_item_unconfirmed_quantity,
    
    
    mla_delivery_items_workflows_received.confirmed_quantity AS dn_item_confirmed_quantity,
    mla_delivery_items_workflows_received.rejected_quantity AS dn_item_rejected_quantity,
    mla_delivery_items_workflows_received.status AS dn_item_status
    
	
FROM mla_purchase_request_items

/* PURCHASE REQUEST */
LEFT JOIN mla_purchase_requests
ON mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_purchase_requests_workflows
ON mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_users
ON mla_users.id = mla_purchase_requests.requested_by

/* 1 - 1 ONLY*/
LEFT JOIN mla_departments_members
ON mla_users.id = mla_departments_members.user_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_departments
ON mla_departments_members.department_id = mla_departments.id
/* PURCHASE REQUEST */	
	
/* total confirmed, rejected and unconfirmed DN */
LEFT JOIN
(
	SELECT
		mla_delivery_items.pr_item_id,
		mla_delivery_items.po_item_id,
        mla_delivery_items_workflows.status AS last_dn_status,
		
		SUM(mla_delivery_items.delivered_quantity) AS total_received_quantity,
        IFNULL(SUM(CASE WHEN mla_delivery_items_workflows.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END),0) AS unconfirmed_quantity,
		IFNULL(SUM(mla_delivery_items_workflows.confirmed_quantity),0) AS confirmed_quantity,
		IFNULL(SUM(mla_delivery_items_workflows.rejected_quantity),0) AS rejected_quantity
	FROM mla_delivery_items
    
    LEFT JOIN mla_delivery_items_workflows
    ON mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
    
	GROUP BY mla_delivery_items.pr_item_id
)
AS mla_delivery_items_workflows
ON mla_delivery_items_workflows.pr_item_id = mla_purchase_request_items.id
	
/* Last Article DN */
LEFT JOIN mla_articles_last_dn
ON mla_articles_last_dn.article_id = mla_purchase_request_items.article_id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_article
ON mla_delivery_items_workflows_article.id = mla_articles_last_dn.last_workflow_id
		
LEFT JOIN mla_delivery_items AS mla_delivery_items_article
ON mla_delivery_items_article.id = mla_delivery_items_workflows_article.dn_item_id
       
LEFT JOIN mla_vendors AS mla_vendor_article
ON mla_delivery_items_article.vendor_id = mla_vendor_article.id
/* Last Article DN */


/* Last SP DN */
LEFT JOIN mla_spareparts_last_dn
ON mla_spareparts_last_dn.sparepart_id = mla_purchase_request_items.sparepart_id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_sp
ON mla_delivery_items_workflows_sp.id = mla_spareparts_last_dn.last_workflow_id
		
LEFT JOIN mla_delivery_items AS mla_delivery_items_sp
ON mla_delivery_items_sp.id = mla_delivery_items_workflows_sp.dn_item_id
        
LEFT JOIN mla_vendors AS mla_vendor_sp
ON mla_delivery_items_sp.vendor_id = mla_vendor_sp.id
/* Last SP DN */
 
/* SP */
LEFT JOIN mla_spareparts
ON mla_spareparts.id = mla_purchase_request_items.sparepart_id


/* PO ITEMS 1-1*/
LEFT JOIN mla_po_item
ON mla_po_item.pr_item_id = mla_purchase_request_items.id

LEFT JOIN mla_vendors AS mla_vendors_po
ON mla_vendors_po.id = mla_po_item.vendor_id

LEFT JOIN mla_delivery_items
ON mla_delivery_items.po_item_id = mla_po_item.id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_received
ON mla_delivery_items_workflows_received.id = mla_delivery_items.last_workflow_id

LEFT JOIN mla_vendors AS mla_vendors_dn
ON mla_vendors_dn.id = mla_delivery_items.vendor_id

/* PO ITEMS 1-1*/

WHERE 1
AND mla_purchase_requests_workflows.status IS NOT NULL
AND mla_delivery_items.po_item_id>0	


/* ALL PR ITEMS*/

AND (CASE WHEN (mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0  
	THEN mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0)  ELSE 0 END) >0


