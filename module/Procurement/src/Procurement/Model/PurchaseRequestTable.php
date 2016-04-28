<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PurchaseRequest;	


class PurchaseRequestTable {
	
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
	
	
	public function add(PurchaseRequest $input) {
		$data = array (
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
				'released_on' => $input->released_on,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(PurchaseRequest $input, $id) {
		
		$data = array (
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
				'released_on' => $input->released_on,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	/**Get all PR of User
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
	
	
	/**Get all PR of User
	 * 
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRof($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		
		$sql = "SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
FROM mla_purchase_requests as TB1				
JOIN 
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
				
		WHERE TB1.requested_by = ". $user_id;
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
	 * Get PR
	 * @param unknown $id
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
	public function getPR($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
FROM mla_purchase_requests as TB1				
JOIN 
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
		
		WHERE TB1.id = ". $id;
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
}