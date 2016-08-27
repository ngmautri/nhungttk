<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PurchaseRequest;

class PurchaseRequestTable {
	protected $tableGateway;
	private $sql_GetToTalPRThisYear = "Select
count(*) as total_pr_this_year,
mla_purchase_requests.pr_of_department_short_name
from
(
	select
		mla_purchase_requests.*,
		year(mla_purchase_requests.requested_on) as pr_year,
		
		concat (mla_users.firstname,' ',mla_users.lastname ) as requester_name,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_short_name as pr_of_department_short_name,
		mla_users.department_status as pr_of_department_status
	from 
	(
	select
		mla_purchase_requests.*,
		mla_purchase_requests_workflows.status as pr_last_status,
		mla_purchase_requests_workflows.updated_by as pr_last_status_by,
		mla_purchase_requests_workflows.updated_on as pr_last_status_on
		 
	from mla_purchase_requests

	left join mla_purchase_requests_workflows
		on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id


	)
	as mla_purchase_requests

	left join
	(
	   /**USER-DEPARTMENT beginns*/
		select 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname, 
			mla_departments_members_1.*
		from mla_users
		join 
		(	select 
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name as department_name,
				mla_departments.short_name as department_short_name,
				mla_departments.status as department_status
			from mla_departments_members
			join mla_departments on mla_departments_members.department_id = mla_departments.id
		) as mla_departments_members_1 
		on mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/

	) 
	as mla_users
		
		on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests

where 1
and pr_year = year(now())";
	private $sql_GetPR = "
SELECT
*
from
(
	select
	mla_purchase_requests.*,
	mla_purchase_request_items.*,
	mla_purchase_requests_workflows.status as pr_status,
	mla_purchase_requests_workflows.updated_on as pr_updated_on,
	year(mla_purchase_requests.requested_on) as pr_year
	from mla_purchase_requests
	left join
	(
		select 
			mla_purchase_requests.id as pr_id,
			count(*) as totalItems
		from mla_purchase_request_items 
		join mla_purchase_requests 
		on mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
		group by mla_purchase_requests.id
	) 
	as mla_purchase_request_items
		on mla_purchase_request_items.pr_id = mla_purchase_requests.id
	left join mla_purchase_requests_workflows
		on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id
)
as mla_purchase_requests
WHERE 1			
";
	private $sql_GetPurchaseRequests = "
select
		mla_purchase_requests.*,
		year(mla_purchase_requests.requested_on) as pr_year,
        month(mla_purchase_requests.requested_on) as pr_month,
		concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_status as pr_of_department_status,
        ifnull(mla_purchase_request_items.total_items,0) as total_items,
		ifnull( mla_purchase_request_items_pending.total_pending_items,0) as total_pending_items,
        
		mla_purchase_requests_workflows.status as pr_last_status,
		mla_purchase_requests_workflows.updated_by as pr_last_status_by,
		mla_purchase_requests_workflows.updated_on as pr_last_status_on
		
from mla_purchase_requests

/* Last Status*/
left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

left join
(
		/**USER-DEPARTMENT beginns*/
		select 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname, 
			mla_departments_members_1.*
		from mla_users
		join 
		(	select 
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name as department_name,
				mla_departments.status as department_status
			from mla_departments_members
			join mla_departments on mla_departments_members.department_id = mla_departments.id
		) as mla_departments_members_1 
		on mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/

	) 
as mla_users
on mla_users.user_id = mla_purchase_requests.requested_by

/* total items*/
left join
(
	select 
		mla_purchase_requests.id as pr_id,
		count(*) as total_items
	from mla_purchase_request_items 
    
	join mla_purchase_requests 
	on mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
	group by mla_purchase_requests.id
) 
as mla_purchase_request_items
on mla_purchase_request_items.pr_id = mla_purchase_requests.id

/* total pending items*/
left join
(
   select 
		mla_purchase_request_items.purchase_request_id as pr_id,
        mla_purchase_request_items.quantity,
		count(mla_purchase_request_items.id) as total_pending_items
	       
	from mla_purchase_request_items 
    
	/* total confirmed and rejected DN */
	left join
	(
		select
			mla_delivery_items_workflows.pr_item_id,
			sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity,
			sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
 			from mla_delivery_items_workflows
		group by mla_delivery_items_workflows.pr_item_id
	)as mla_delivery_items_workflows
	on mla_delivery_items_workflows.pr_item_id = mla_purchase_request_items.id
    
    /* pending*/
	where (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))>0
    
    group by mla_purchase_request_items.purchase_request_id
)
as mla_purchase_request_items_pending
on mla_purchase_request_items_pending.pr_id = mla_purchase_requests.id


WHERE 1
And mla_purchase_requests_workflows.status is not null
			
			";
	
	/**
	 *
	 * @param TableGateway $tableGateway        	
	 */
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 *
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	public function get($id) {
		$id = ( int ) $id;
		
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/**
	 *
	 * @param PurchaseRequest $input        	
	 * @return number
	 */
	public function add(PurchaseRequest $input) {
		$data = array (
				'seq_number_of_year' => $input->seq_number_of_year,
				'auto_pr_number' => $input->auto_pr_number,
				
				'pr_number' => $input->pr_number,
				'name' => $input->name,
				'description' => $input->description,
				
				'requested_by' => $input->requested_by,
				'requested_on' => date ( 'Y-m-d H:i:s' ),
				
				'verified_by' => $input->verified_by,
				'verified_on' => $input->verified_on,
				
				'approved_by' => $input->approved_by,
				'approved_on' => $input->approved_on,
				
				'released_by' => $input->released_by,
				'released_on' => $input->released_on 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param PurchaseRequest $input        	
	 * @param unknown $id        	
	 */
	public function update(PurchaseRequest $input, $id) {
		$data = array (
				'seq_number_of_year' => $input->seq_number_of_year,
				'auto_pr_number' => $input->auto_pr_number,
				
				'pr_number' => $input->pr_number,
				'name' => $input->name,
				'description' => $input->description,
				
				'requested_by' => $input->requested_by,
				'reqeusted_on' => date ( 'Y-m-d H:i:s' ),
				
				'verified_by' => $input->verified_by,
				'verified_on' => $input->verified_on,
				
				'approved_by' => $input->approved_by,
				'approved_on' => $input->approved_on,
				
				'released_by' => $input->released_by,
				'released_on' => $input->released_on 
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	
	/**
	 *
	 * @param unknown $id        	
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	/**
	 *
	 * @param unknown $pr_id        	
	 * @param unknown $last_workflow_id        	
	 */
	public function updateLastWorkFlow($pr_id, $last_workflow_id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "update mla_purchase_requests
		set last_workflow_id = " . $last_workflow_id . " where id = " . $pr_id;
		
		// echo $sql;
		
		$statement = $adapter->query ( $sql );
		$statement->execute ();
	}
	
	/**
	 * Get all PR of User
	 *
	 * @param unknown $user_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getAllSumbittedPRs() {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
FROM mla_purchase_requests as TB1				
JOIN 
(select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1 
join mla_purchase_requests as t2 
on t1.purchase_request_id = t2.id
group by t2.id) as TB2
ON TB2.pr_id =  TB1.id        
JOIN    
(select lt1.status,lt1.purchase_request_id, lt2.last_change from mla_purchase_requests_workflows as lt1
join
(select tt1.purchase_request_id,max(tt1.updated_on) as last_change from mla_purchase_requests_workflows as tt1
Group by tt1.purchase_request_id) as lt2
ON lt1.updated_on = lt2.last_change) as TB3

ON TB1.id =  TB3.purchase_request_id
		
LEFT join mla_users as TB4
on TB4.id = TB1.requested_by";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @param unknown $department_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getAllSumbittedPurchaseRequests($last_status, $user_id, $department_id) {
		$adapter = $this->tableGateway->adapter;
		
		if ($last_status == '') {
			$last_status_filter = '1';
		} else {
			$last_status_filter = "lt4.last_status = '" . $last_status . "'";
		}
		
		if ($user_id === '') {
			if ($department_id === '') {
				$filter = '';
			} else {
				$filter = 'WHERE A2.department_id =' . $department_id;
			}
		} else {
			$filter = 'WHERE A2.user_id =' . $user_id;
			if ($department_id != '') {
				$filter = $filter . ' AND A2.department_id =' . $department_id;
			}
		}
		
		$sql = "
SELECT 
    TB1.*, TB2.*, TB3.*, TB4.*, concat(TB4.firstname, ' ', TB4.lastname) as requester
FROM
    mla_purchase_requests AS TB1
        
        /* total pr items*/
        JOIN
    (SELECT 
        t2.id AS pr_id, COUNT(*) AS tItems 
    FROM
        mla_purchase_request_items AS t1
    JOIN mla_purchase_requests AS t2 ON t1.purchase_request_id = t2.id
    GROUP BY t2.id) AS TB2 ON TB2.pr_id = TB1.id
     
     /* Last PR status*/   
        JOIN
    (SELECT 
        *
    FROM
        (SELECT 
			lt1.purchase_request_id,
            lt1.status AS last_status,
            lt2.last_change
    FROM
        (SELECT 
        lt3.*,
            CONCAT(lt3.purchase_request_id, '+++', lt3.updated_on) AS id_updated_on
    FROM
        mla_purchase_requests_workflows AS lt3) AS lt1
    JOIN (SELECT 
        CONCAT(tt1.purchase_request_id, '+++', MAX(tt1.updated_on)) AS id_lastchanged,
            tt1.purchase_request_id,
            MAX(tt1.updated_on) AS last_change
    FROM
        mla_purchase_requests_workflows AS tt1
    GROUP BY tt1.purchase_request_id) AS lt2 ON lt1.id_updated_on = lt2.id_lastchanged) AS lt4
    WHERE " . $last_status_filter . 

		") AS TB3 ON TB1.id = TB3.purchase_request_id
         JOIN
      /* USER WITH DEPARTMENT*/   
    (SELECT 
        A1.title, A1.firstname, A1.lastname, A2.*
    FROM
        mla_users AS A1
    JOIN (SELECT 
			B1.department_id,
            B1.user_id,
            B2.name AS department_name,
            B2.status AS department_status
    FROM
        mla_departments_members AS B1
    JOIN mla_departments AS B2 ON B1.department_id = B2.id) 
    
    AS A2 ON A1.id = A2.user_id
    "
    /* Filer:  WHERE A2.user_id =39;  WHERE A2.department_id =2*/
    . $filter . "
    ) AS TB4 ON TB4.user_id = TB1.requested_by";
		
		// echo $sql;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @param unknown $pr_status        	
	 * @param unknown $limit        	
	 * @param unknown $offset        	
	 * @param unknown $order_by        	
	 */
	public function getMyPR($user_id, $pr_status, $limit, $offset, $order_by) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = $this->sql_GetPR;
		
		if ($user_id > 0) {
			$sql = $sql . " AND mla_purchase_requests.requested_by = " . $user_id;
		}
		
		if ($pr_status != "" || $pr_status != null) {
			$sql = $sql . " AND mla_purchase_requests.pr_status = '" . $pr_status . "'";
		}
		
		$sql = $sql . " ORDER BY mla_purchase_requests.requested_on DESC";
		
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql . " OFFSET " . $offset;
		}
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * Get all PR of User
	 *
	 * @param unknown $user_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRof($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
FROM mla_purchase_requests as TB1				
LEFT JOIN 
	(select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1 
	join mla_purchase_requests as t2 
	on t1.purchase_request_id = t2.id
	group by t2.id) as TB2
ON TB2.pr_id =  TB1.id        
LEFT JOIN    
	(select lt1.status,lt1.purchase_request_id, lt2.last_change, lt2.changed_by from mla_purchase_requests_workflows as lt1
	join
	(select tt1.purchase_request_id,max(tt1.updated_on) as last_change, tt1.updated_by, concat(tt2.firstname, ' ', tt2.lastname) as changed_by from mla_purchase_requests_workflows as tt1
	left join mla_users as tt2
	on tt2.id = tt1.updated_by
	Group by tt1.purchase_request_id
) as lt2
ON lt1.updated_on = lt2.last_change) as TB3

ON TB1.id =  TB3.purchase_request_id
		
LEFT join mla_users as TB4
on TB4.id = TB1.requested_by
				
		WHERE TB1.requested_by = " . $user_id;
		/*
		 * $sql = "SELECT *
		 * FROM mla_purchase_requests as t1
		 * LEFT JOIN mla_purchase_request_items as t2
		 * on t2.purchase_request_id = t1.id
		 * WHERE requested_by = ". $user_id;
		 */
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * Get PR
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
	public function getPR($id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
FROM mla_purchase_requests as TB1				
LEFT JOIN 
(select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1 
join mla_purchase_requests as t2 
on t1.purchase_request_id = t2.id
group by t2.id) as TB2
ON TB2.pr_id =  TB1.id        
LEFT JOIN    
(select lt1.status,lt1.purchase_request_id, lt2.last_change, lt2.changed_by from mla_purchase_requests_workflows as lt1
join
(select tt1.purchase_request_id,max(tt1.updated_on) as last_change, tt1.updated_by, concat(tt2.firstname, ' ', tt2.lastname) as changed_by from mla_purchase_requests_workflows as tt1
left join mla_users as tt2
on tt2.id = tt1.updated_by
Group by tt1.purchase_request_id
) as lt2
ON lt1.updated_on = lt2.last_change) as TB3

ON TB1.id =  TB3.purchase_request_id
		
LEFT join mla_users as TB4
on TB4.id = TB1.requested_by
		
		WHERE TB1.id = " . $id;
		/*
		 * $sql = "SELECT *
		 * FROM mla_purchase_requests as t1
		 * LEFT JOIN mla_purchase_request_items as t2
		 * on t2.purchase_request_id = t1.id
		 * WHERE requested_by = ". $user_id;
		 */
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		$row = $resultSet->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/**
	 * Get all PR of User
	 *
	 * @param unknown $user_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getTotalPROfYear($user_id) {
		$adapter = $this->tableGateway->adapter;
		$sql = $this->sql_GetToTalPRThisYear;
		$sql = $sql . "AND mla_purchase_requests.pr_of_department_id
				= (SELECT department_id from mla_departments_members
				where user_id = " . $user_id . " Limit 1)";
		
		// echo $sql;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet->current ();
		} else {
			return null;
		}
	}
	
	/**
	 * Get all PR of User
	 *
	 * @param unknown $user_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPurchaseRequests($flow,$last_status, $department_id, $limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		$sql = $this->sql_GetPurchaseRequests;
		
		if ($department_id > 0) {
			$sql = $sql . " AND mla_users.department_id = " . $department_id;
		}
		
		if($last_status=='Fulfilled'){			
			$sql = $sql . " AND ifnull( mla_purchase_request_items_pending.total_pending_items,0)=0";
		}
		
		if($last_status=='Pending'){
			$sql = $sql . " AND ifnull( mla_purchase_request_items_pending.total_pending_items,0)>0";
		}
		
		if ($flow != "all") {
			$sql = $sql . " AND mla_purchase_requests_workflows.status  = '" . $flow . "'";
		}
		
		$sql = $sql . " ORDER BY  mla_purchase_requests.requested_on DESC";
		
		
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql . " OFFSET " . $offset;
		}
		
		
		
		// echo $sql;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet;
		} else {
			return null;
		}
	}
	
	/**
	 * Get all PR of User
	 *
	 * @param unknown $user_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getSubmittedPR($pr_id) {
		$adapter = $this->tableGateway->adapter;
		$sql = "/* PRINT SUBMITTED PR */
select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.seq_number_of_year,
    mla_purchase_requests.auto_pr_number,
	
    mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.status,
	mla_purchase_requests.updated_on,
	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	mla_purchase_requests.email,
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status
	
	
from mla_purchase_request_items
	
/* purchase requests*/
left join
(
	select
		mla_purchase_requests.*,
		year(mla_purchase_requests.requested_on) as pr_year,
	
		concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
		mla_users.email,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_status as pr_of_department_status
	
	from
	(
			/* PURCHASE WITH STATUS = SUBMITTED*/
			select
			*
			from
			(
				select
					mla_purchase_requests.*,
					mla_purchase_requests_workflows.status,
					mla_purchase_requests_workflows.updated_on
				from mla_purchase_requests
				left join mla_purchase_requests_workflows
					on mla_purchase_requests_workflows.purchase_request_id = mla_purchase_requests.id
			)
			as mla_purchase_requests
			where 1
			and mla_purchase_requests.status='submitted'
			/* PURCHASE WITH STATUS = SUBMITTED*/
	
	)
	as mla_purchase_requests
	
	left join
	(
	
		
		/**USER-DEPARTMENT beginns*/
		select
			mla_users.title,
			mla_users.firstname,
			mla_users.lastname,
			mla_users.email,
			mla_departments_members_1.*
		from mla_users
		join
		(	select
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name as department_name,
				mla_departments.status as department_status
			from mla_departments_members
			join mla_departments on mla_departments_members.department_id = mla_departments.id
		) as mla_departments_members_1
		on mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/
	
	)
	as mla_users
		on mla_users.user_id = mla_purchase_requests.requested_by
	)
	as mla_purchase_requests
	on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
)
as mla_purchase_request_items
Where 1
and purchase_request_id = " . $pr_id . ' ORDER BY mla_purchase_request_items.EDT';
		
		// echo $sql;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet;
		} else {
			return null;
		}
	}
}