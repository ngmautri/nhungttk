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
select TT3.pr_number, TT3.description, TT3.requested_on, TT3.tItems,TT3.status,TT3.last_change,TT3.requester , TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
	
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id

JOIN

(SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester		
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
on TB4.id = TB1.requested_by) as TT3
	
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

}