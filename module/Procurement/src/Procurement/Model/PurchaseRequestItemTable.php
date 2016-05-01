<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Procurement\Model\PurchaseRequestItem;

class PurchaseRequestItemTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	
	public function get($id){
		
		$id  = (int) $id;
		
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	/**
	 * 
	public $id;
	public $purchase_request_id;
	public $priority;
	public $name;
	public $description;
	
	public $code;	
	public $keywords;
	
	public $unit;
	public $quantity;
	public $EDT;

	public $article_id;
	public $sparepart_id;
	public $asset_id;
	public $other_res_id;
	
	public $remarks;
	public $created_on;
	 * @param PurchaseRequestItem $input
	 * @return number
	 */
	public function add(PurchaseRequestItem $input) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				
				'code' => $input->code,
				'keywords' => $input->keywords,
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				
				'article_id' => $input->article_id,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
				
				'remarks' => $input->remarks,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	
	public function update(PurchaseRequestItem $input, $id) {
		
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				
				'code' => $input->code,
				'keywords' => $input->keywords,
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				
				'article_id' => $input->article_id,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
				
				'remarks' => $input->remarks,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				
				
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	/*
	 * GET ITEM
	 */
	public function getItem($id) {
		$adapter = $this->tableGateway->adapter;
	
		/*
			$sql = "SELECT *
			FROM mla_purchase_request_items
			WHERE purchase_request_id = ". $pr .
			" ORDER BY EDT ASC";
			*/
	
	
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id
	
left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id
	
WHERE TT1.id = ". $id .
	" ORDER BY TT1.EDT ASC";
	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet->current();
	}
	
	public function getItemsByPR($pr) {
		$adapter = $this->tableGateway->adapter;
		
		/*
		$sql = "SELECT *
		FROM mla_purchase_request_items 
		WHERE purchase_request_id = ". $pr .
		" ORDER BY EDT ASC";
		*/
		
		
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by 
from mla_delivery_items as T1
left join mla_purchase_request_items as T2 
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id

left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id
				
WHERE TT1.purchase_request_id = ". $pr .
" ORDER BY TT1.EDT ASC";
		
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	public function getItems() {
		$adapter = $this->tableGateway->adapter;
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id
	
left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id";

//"ORDER BY TT1.EDT ASC";
	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}

	
	/**
	 * =====================
	 */
	public function getPRItems() {
		$adapter = $this->tableGateway->adapter;
		$sql = "
select TT3.pr_number, TT3.user_id, TT3.requester_firstname, TT3.requester_lastname , TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join

(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id
	
left join 

(select ttt01.*, ttt02.id as user_id, ttt02.firstname as requester_firstname, ttt02.lastname as requester_lastname from mla_purchase_requests as ttt01
left join mla_users as ttt02
on ttt01.requested_by = ttt02.id) as TT3

on TT1.purchase_request_id = TT3.id
ORDER BY TT1.EDT ASC
		";
	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * =====================
	 */
	public function getSubmittedPRItems() {
		$adapter = $this->tableGateway->adapter;
		$sql = "
				
SElECT 
	mla_purchase_request_items.*,
   mla_delivery_items_1.delivered_quantity,
    
	mla_purchase_requests_1.last_pr_status,
	mla_purchase_requests_1.last_pr_status_on,
  	mla_purchase_requests_1.department_id,
 	mla_purchase_requests_1.requested_by,
 
	
    mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
	mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
 
FROM mla_purchase_request_items

LEFT JOIN
(
	/*Delivered Quantity*/
	select 
		mla_purchase_request_items.id as pr_item_id, 
	   sum(mla_delivery_items.delivered_quantity) as delivered_quantity
		from mla_delivery_items

	left join mla_purchase_request_items
	On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

	left join mla_delivery
	on mla_delivery_items.delivery_id = mla_delivery.id

	group by mla_delivery_items.pr_item_id
	/*Delivered Quantity*/

) AS mla_delivery_items_1

ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

/* JOIN: select only submitted PR */
JOIN 
(
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
            
            /* FILTER  
            WHERE mla_purchase_requests_workflows_1.status='Approved'*/
           
            
            
			/* Last Workflow changed PR) */
		) AS mla_purchase_requests_workflows_1_1

		ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

		JOIN
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
                
                /* Filter*/
                WHERE mla_departments_members.department_id = 2
                
			) AS mla_departments_members_1 
			ON mla_users.id = mla_departments_members_1.user_id
			/**USER-DEPARTMENT ends*/
            
		) AS mla_users_1
		ON mla_users_1.user_id = mla_purchase_requests.requested_by

) AS mla_purchase_requests_1

ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

LEFT JOIN
(
	/* Last Workflow changed PR ITEM) */
	select
		mla_purchase_requests_items_workflows_1.*
	from 

		(select 
		mla_purchase_requests_items_workflows.*,
		concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
		from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

	join

		(select 
			max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
			mla_purchase_requests_items_workflows.pr_item_id
			 from mla_purchase_requests_items_workflows
		group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

	on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
	/* Last Workflow changed PR ITEM) */

) AS mla_purchase_requests_items_workflows_1_1

ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

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
) as mla_users_1

ON mla_users_1.user_id = mla_purchase_request_items.created_by
				
ORDER BY mla_purchase_request_items.EDT ASC
		";
	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	

	/**
	 * Get all submitted PR ITEM, with filter
	 */
	public function getAllSubmittedPRItems($last_status, $user_id,$department_id,$balance,$limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		
		if ($balance==-1){
			$t_balance = ' 1';
		}
	
		if ($balance==0){
			$t_balance = ' mla_purchase_request_items_1.balance = 0';
		}
		if ($balance==1){
			$t_balance = ' mla_purchase_request_items_1.balance >0';
		}
		
		if ($limit==0){
			$t_limit = '';
		}else{
			$t_limit = ' LIMIT '.$limit;
		}
		
		if ($offset==0){
			$t_offset = '';
		}else{
			$t_offset = ' OFFSET '.$offset;
		}
		
		if ($last_status == ''){
			$last_status_filter = 'WHERE 1';
		}else{
			$last_status_filter = "WHERE mla_purchase_requests_workflows_1.status = '" . $last_status ."'";
		}
		
	if ($user_id === ''){
			if ($department_id === ''){
				$filter='';
			}else{
				$filter = 'WHERE mla_departments_members.department_id ='.$department_id ;
			}
		}else{
			$filter = 'WHERE mla_departments_members.user_id ='.$user_id ;
			if ($department_id != ''){
				$filter = $filter. ' AND mla_departments_members.department_id ='.$department_id ;
			}
		}
		
		$sql = "
SELECT 
*
FROM 
(
	SElECT 
		mla_purchase_request_items.*,
		ifnull(mla_delivery_items_1.delivered_quantity,0) as delivered_quantity,
		(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_1.delivered_quantity,0)) as balance,
		mla_purchase_requests_1.pr_number,
		mla_purchase_requests_1.last_pr_status,
		mla_purchase_requests_1.last_pr_status_on,
		mla_purchase_requests_1.department_id,
		mla_purchase_requests_1.department_name,
		mla_purchase_requests_1.requested_by,
	 
		
		mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
		mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
	 
	FROM mla_purchase_request_items

	LEFT JOIN
	(
		/*Delivered Quantity*/
		select 
			mla_purchase_request_items.id as pr_item_id, 
			sum(mla_delivery_items.delivered_quantity) as delivered_quantity
			from mla_delivery_items

		left join mla_purchase_request_items
		On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

		left join mla_delivery
		on mla_delivery_items.delivery_id = mla_delivery.id

		group by mla_delivery_items.pr_item_id
		/*Delivered Quantity*/
	) AS mla_delivery_items_1

	ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

	/* JOIN: select only submitted PR */
	JOIN 
	(
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
				
				/* FILTER  
				WHERE mla_purchase_requests_workflows_1.status='Approved'*/
			    "
			    .$last_status_filter.
				"
				
				/* Last Workflow changed PR) */
			) AS mla_purchase_requests_workflows_1_1

			ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

			JOIN
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
					
					/* Filter*/
					 "
			    	.$filter.
					"
					
				) AS mla_departments_members_1 
				ON mla_users.id = mla_departments_members_1.user_id
				/**USER-DEPARTMENT ends*/
				
			) AS mla_users_1
			ON mla_users_1.user_id = mla_purchase_requests.requested_by

	) AS mla_purchase_requests_1

	ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

	LEFT JOIN
	(
		/* Last Workflow changed PR ITEM) */
		select
			mla_purchase_requests_items_workflows_1.*
		from 

			(select 
			mla_purchase_requests_items_workflows.*,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
			from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

		join

			(select 
				max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
				concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
				mla_purchase_requests_items_workflows.pr_item_id
				 from mla_purchase_requests_items_workflows
			group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

		on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		 group by mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		/* Last Workflow changed PR ITEM) */

	) AS mla_purchase_requests_items_workflows_1_1

	ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

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
	) as mla_users_1

	ON mla_users_1.user_id = mla_purchase_request_items.created_by
) AS mla_purchase_request_items_1

WHERE " . $t_balance . " ORDER BY mla_purchase_request_items_1.EDT ASC
" . $t_limit . $t_offset;
		
		//echo($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	
	/**
	 * =====================
	 */
	public function getMySubmittedPRItems($user_id) {
		$adapter = $this->tableGateway->adapter;
		$sql = "
SELECT TT3.pr_number, TT3.description, TT3.requested_on, TT3.tItems,TT3.status,TT3.last_change,TT3.requester , TT3.user_id, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1

LEFT JOIN
	
	(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
	from mla_delivery_items as T1
	left join mla_purchase_request_items as T2
	On T2.id = T1.pr_item_id
	left join mla_delivery as T3
	on T1.delivery_id = t3.id
	group by T1.pr_item_id) as TT2

On TT2.id = TT1.id
	
JOIN
	
	(SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester, TB4.id as user_id FROM mla_purchase_requests as TB1
	JOIN
		(select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1
		join mla_purchase_requests as t2
		on t1.purchase_request_id = t2.id    
		group by t2.id    
		) as TB2        
    ON TB2.pr_id =  TB1.id
   
	/* important, change to JOIN to show only summbited */
	JOIN				
		(select lt1.status,lt1.purchase_request_id, lt2.last_change from mla_purchase_requests_workflows as lt1
		Join 
		(select tt1.purchase_request_id,max(tt1.updated_on) as last_change from mla_purchase_requests_workflows as tt1
		Group by tt1.purchase_request_id) as lt2
		ON lt1.updated_on = lt2.last_change) as TB3

	ON TB1.id =  TB3.purchase_request_id

	
	LEFT join mla_users as TB4
	on TB4.id = TB1.requested_by
    
    /*CHANGE*/
    Where TB1.requested_by = " . $user_id . "
    ) as TT3
  	
on TT1.purchase_request_id = TT3.id
ORDER BY TT1.EDT ASC
		";
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * ======================
	 * 
	 * @param unknown $item
	 */
	public function getDeliveredOfItem($item) {
		$adapter = $this->tableGateway->adapter;
	
		
	$sql = "			
select sum(t1.delivered_quantity) as delivered_quantity
from mla_delivery_items as T1
left join mla_purchase_request_items as T2 
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
WHERE T1.pr_item_id = ". $item .
" group by T1.pr_item_id";
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		$r  = $resultSet->current();
		if($r) 
		{
			return (int) $r["delivered_quantity"];
		}
	return 0;
	
	}
	
	/**
	 * ==============================================
	 */
	public function getLastStatusPRItems() {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
/* Last Workflow changed PR ITEM) */
select
	mla_purchase_requests_items_workflows_1.*
from 

	(select 
	mla_purchase_requests_items_workflows.*,
	concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
	from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

join

	(select 
		max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
		concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
		mla_purchase_requests_items_workflows.pr_item_id
		 from mla_purchase_requests_items_workflows
	group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
"
;
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
			
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;	
	}
}