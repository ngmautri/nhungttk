<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\Delivery;	


class DeliveryTable {
	
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
	
	
	public function add(Delivery $input) {
		$data = array (
				'dn_number' => $input->dn_number,
				'description' => $input->description,
				
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(PurchaseRequest $input, $id) {
		
		$data = array (
				'dn_number' => $input->dn_number,
				'description' => $input->description,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	

	/**
	 * 
	 * @param unknown $user_id 
	 * @param unknown $limit
	 * @param unknown $offset
	 */
	public function getDeliveries($dn_id,$user_id, $limit, $offset) {

		$adapter = $this->tableGateway->adapter;
		
		if($dn_id==0):
			$t_filter_dn ='';
		else:
			$t_filter_dn = ' WHERE mla_delivery.id = '. $dn_id;
			$user_id = 0;
			$limit = 0;
			$offset= 0;
		endif;
		
		
		if($user_id == 0):
			$t_filter_user = '';
		else:
			$t_filter_user = ' WHERE mla_delivery.created_by = '. $user_id;
		endif;
		
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
			
		$sql = "
SELECT 
	mla_delivery.*,
    year(mla_delivery.created_on) as dn_year,
    mla_delivery_items_1.totalItems,
    mla_delivery_1.status as dn_last_status,
	mla_delivery_1.dn_last_change,
    mla_delivery_1.dn_last_changed_by,
    mla_users_1.*
    
FROM mla_delivery 

LEFT JOIN
(

	SELECT 
	*
	FROM
	(
		SELECT 
		mla_delivery.*,
		mla_delivery_workflows_1.status,
		mla_delivery_workflows_1.dn_id_changed_on,
        mla_delivery_workflows_1.dn_last_changed_by

		FROM mla_delivery

		Left JOin
		(	
			select 
				mla_delivery_workflows.*,
				concat(mla_delivery_workflows.delivery_id,'+++',mla_delivery_workflows.updated_on) as dn_id_changed_on,
                mla_delivery_workflows.updated_by as dn_last_changed_by
			from mla_delivery_workflows
			
		) AS mla_delivery_workflows_1

		on mla_delivery_workflows_1.delivery_id = mla_delivery.id
	) AS mla_delivery_1

	JOIN

	(
		SELECT
			mla_delivery_workflows.delivery_id,
			MAX(mla_delivery_workflows.updated_on) AS dn_last_change,
			CONCAT(mla_delivery_workflows.delivery_id,'+++',MAX(mla_delivery_workflows.updated_on)) AS dn_id_lastchange_on
				
			FROM mla_delivery_workflows
		GROUP BY mla_delivery_workflows.delivery_id
	) AS mla_delivery_workflows_1

	on mla_delivery_workflows_1.dn_id_lastchange_on = mla_delivery_1.dn_id_changed_on
) AS mla_delivery_1
ON mla_delivery_1.id = mla_delivery.id

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
	) AS mla_departments_members_1 
    ON mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
) AS mla_users_1

ON mla_users_1.user_id = mla_delivery.created_by

LEFT JOIN
(
	select 
		mla_delivery.id as dn_id, 
		count(*) as totalItems  
		
		from mla_delivery_items
	join mla_delivery 
	on mla_delivery_items.delivery_id = mla_delivery.id
	group by mla_delivery.id
) AS mla_delivery_items_1

ON mla_delivery_items_1.dn_id = mla_delivery.id"
		. $t_filter_dn
		. $t_filter_user 
		. $t_limit 
		. $t_offset;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
		
		
	}
	
	/**
	 * +++++++++++++++++++++++
	 * @param unknown $user_id
	 */
	public function getDeliveryOf($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		
		$sql = "SELECT *
		FROM mla_delivery as TB1		
		LEFT JOIN (select count(*) as tItems, t2.id as dn_id from mla_delivery_items as t1 
join mla_delivery as t2 
on t1.delivery_id = t2.id
group by t2.id) as TB2
		ON TB2.dn_id =  TB1.id
				
		WHERE TB1.created_by = ". $user_id;
		/*
		$sql = "SELECT *
		FROM mla_purchase_requests as t1
		LEFT JOIN mla_purchase_request_items as t2
		on t2.purchase_request_id = t1.id
		WHERE requested_by = ". $user_id;
		*/
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	public function getDelivery($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "select t1.*, concat(t2.firstname, ' ', t2.lastname) as requester from mla_purchase_requests as t1
		left join mla_users as t2
		on t2.id = t1.requested_by
		WHERE t1.id = ". $id;
		/*
			$sql = "SELECT *
			FROM mla_purchase_requests as t1
			LEFT JOIN mla_purchase_request_items as t2
			on t2.purchase_request_id = t1.id
			WHERE requested_by = ". $user_id;
			*/
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		$row = $resultSet->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;		
	}
	
	/**
	 * 
	 * @param unknown $dn_id
	 * @param unknown $last_workflow_id
	 */
	public function updateLastWorkFlow($dn_id,$last_workflow_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql =
		"update mla_delivery
		set last_workflow_id = " . $last_workflow_id.
		" where id = " . $dn_id;
	
		//echo $sql;
	
		$statement = $adapter->query ( $sql );
		$statement->execute ();
	}
	
	/**
	 * ===================================
	 * @param unknown $id
	 * @throws \Exception
	 */
	public function getDeliveryItems($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "
select
	mla_delivery.*,
	mla_delivery_items.id as dn_item_id,				
    mla_delivery_items.pr_item_id,
    mla_delivery_items.name as delivered_item_name,
    mla_delivery_items.delivered_quantity,
    mla_delivery_items.price,
	mla_delivery_items.currency,
	mla_delivery_items.vendor_id
from mla_delivery
join mla_delivery_items
on mla_delivery.id =  mla_delivery_items.delivery_id

join
(select mla_delivery.id as dn_id,count(*) as totalItems from mla_delivery_items
join mla_delivery
on mla_delivery_items.delivery_id = mla_delivery.id
group by mla_delivery.id) as mla_delivery_1

on mla_delivery_1.dn_id = mla_delivery.id
		WHERE mla_delivery.id = ". $id;
		/*
		 $sql = "SELECT *
		 FROM mla_purchase_requests as t1
		 LEFT JOIN mla_purchase_request_items as t2
		 on t2.purchase_request_id = t1.id
		 WHERE requested_by = ". $user_id;
		 */
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
	
		return $resultSet;
	}
	
	/**
	 * ===================================
	 * @param unknown $id
	 * @throws \Exception
	 */
	public function test() {
		$adapter = $this->tableGateway->adapter;
			
		/*call stored procedured.*/
		$sql = "call getDelivers()";
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
	
		return $resultSet;
	}
	

}