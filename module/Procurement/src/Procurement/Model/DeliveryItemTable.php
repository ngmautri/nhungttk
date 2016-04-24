<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\DeliveryItem;	


class DeliveryItemTable {
	
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
	
	/*
	 * public $id;
	public $delivery_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	
	public $unit;
	public $delivered_quantity;

	public $price;
	public $currency;
	
	public $vendor_id;
	public $notes;
	 */
	public function add(DeliveryItem $input) {
		$data = array (
				'delivery_id' => $input->delivery_id,
				'pr_item_id' => $input->pr_item_id,
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				'delivered_quantity' => $input->delivered_quantity,
				'price' => $input->price,
				'currency' => $input->currency,
				'vendor_id' => $input->vendor_id,
				'notes' => $input->notes,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(DeliveryItem $input, $id) {
		
		$data = array (
				'delivery_id' => $input->delivery_id,
				'pr_item_id' => $input->pr_item_id,
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				'delivered_quantity' => $input->delivered_quantity,
				'price' => $input->price,
				'currency' => $input->currency,
				'vendor_id' => $input->vendor_id,
				'notes' => $input->notes,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function getItemsByDN($dn) {
		$adapter = $this->tableGateway->adapter;
	
		/*
			$sql = "SELECT *
			FROM mla_purchase_request_items
			WHERE purchase_request_id = ". $pr .
			" ORDER BY EDT ASC";
			*/
	
	
		$sql = "
SELECT * from mla_delivery_items as TT1
WHERE TT1.delivery_id =". $dn;	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
}