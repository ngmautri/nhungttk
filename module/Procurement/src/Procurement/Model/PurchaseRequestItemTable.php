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
	
	
	public function add(PurchaseRequestItem $input) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				'code' => $input->code,
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
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
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
				
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