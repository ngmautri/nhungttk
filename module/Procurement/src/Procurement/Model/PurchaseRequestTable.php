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
	
	/**
	 * 
	 * @param unknown $user_id
	 * @param unknown $department_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getAllSumbittedPurchaseRequests($last_status, $user_id,$department_id) {
		$adapter = $this->tableGateway->adapter;
		
		if ($last_status == ''){
			$last_status_filter = '1';
		}else{
			$last_status_filter = "lt4.last_status = '" . $last_status ."'";
		}
		
		if ($user_id === ''){
			if ($department_id === ''){
				$filter='';
			}else{
				$filter = 'WHERE A2.department_id ='.$department_id ;
			}
		}else{
			$filter = 'WHERE A2.user_id ='.$user_id ;
			if ($department_id != ''){
				$filter = $filter. ' AND A2.department_id ='.$department_id ;
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
    WHERE "    
   		. $last_status_filter .
   		
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
    . $filter .
   	"
    ) AS TB4 ON TB4.user_id = TB1.requested_by";
		
   		//echo $sql;
	
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