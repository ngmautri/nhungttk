/* PR: with total items, last status, user and department*/

SELECT
	mla_purchase_requests.*,
	mla_purchase_request_items_1.tItems as totalItems,
    mla_purchase_requests_workflows_1_1.status as last_pr_status,
	mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
    mla_users_1.*

FROM mla_purchase_requests

LEFT JOIN
(
	/* TOTAL PR Items*/
    SELECT
		mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
    FROM
        mla_purchase_request_items
    JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
    GROUP BY mla_purchase_requests.id
 	/* TOTAL PR Items*/

) AS mla_purchase_request_items_1
ON mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

JOIN
(
	/* Last Workflow changed PR) */
	select
		mla_purchase_requests_workflows_1.*
	from 

		(select 
		mla_purchase_requests_workflows.*,
		concat(mla_purchase_requests_workflows.purchase_request_id,'+++',mla_purchase_requests_workflows.updated_on) as pr_id_changed_on
		from mla_purchase_requests_workflows) as mla_purchase_requests_workflows_1

	join

		(select 
			max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
			concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
			mla_purchase_requests_workflows.purchase_request_id
			 from mla_purchase_requests_workflows
		group by mla_purchase_requests_workflows.purchase_request_id) AS mla_purchase_requests_workflows_2

	on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
	/* Last Workflow changed PR) */
) AS mla_purchase_requests_workflows_1_1

ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

LEFT JOIN
(
	/**USER-DEPARTMENT beginns*/
    SELECT 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    FROM mla_users
    JOIN 
	(	SELECT 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name AS department_name,
            mla_departments.status AS department_status
		FROM mla_departments_members
		JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
	) AS mla_departments_members_1 
    ON mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
) AS mla_users_1
ON mla_users_1.user_id = mla_purchase_requests.requested_by
