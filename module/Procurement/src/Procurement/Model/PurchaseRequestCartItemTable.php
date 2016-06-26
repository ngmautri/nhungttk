<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Procurement\Model\PurchaseRequestCartItem;

class PurchaseRequestCartItemTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * public $id;
	 * public $purchase_request_id;
	 * public $priority;
	 * public $name;
	 * public $description;
	 *
	 * public $code;
	 * public $keywords;
	 *
	 * public $unit;
	 * public $quantity;
	 * public $EDT;
	 *
	 * public $article_id;
	 * public $sparepart_id;
	 * public $asset_id;
	 * public $other_res_id;
	 *
	 * public $remarks;
	 * public $created_on;
	 *
	 * @param PurchaseRequestItem $input        	
	 * @return number
	 */
	public function add(PurchaseRequestCartItem $input) {
		$data = array (
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
				'status' => $input->status 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param PurchaseRequestItem $input        	
	 * @param unknown $id        	
	 */
	public function update(PurchaseRequestCartItem $input, $id) {
		$data = array (
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
				'status' => $input->status 
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
	 * @param unknown $user_id        	
	 * @return number
	 */
	public function getTotalCartItems($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "select
			count(*) as cart_items
			from mla_purchase_cart
			where 1
			and mla_purchase_cart.status is null
			and mla_purchase_cart.created_by = " . $user_id;
		// echo ($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet->current ()->cart_items;
		} else {
			return 0;
		}
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @return number
	 */
	public function getCartItems($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "select
mla_purchase_cart.*
from mla_purchase_cart
left join mla_articles 
on mla_articles.id = mla_purchase_cart.article_id

left join mla_spareparts
on mla_spareparts.id = mla_purchase_cart.sparepart_id			
			where 1
			and mla_purchase_cart.status is null
			and mla_purchase_cart.created_by = " . $user_id . " order by mla_purchase_cart.edt";
		// echo ($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 * Set All Cart Items as ORDERED
	 *
	 * @param unknown $user_id
	 */
	public function setCartItemsAsOrdered($user_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update
(
   mla_purchase_cart   
) 
set  mla_purchase_cart.status  = 'ORDERED'
where 1
and mla_purchase_cart.status is null
and mla_purchase_cart.created_by = " . $user_id;

		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	

	/**
	 * 
	 * 
	 * @param unknown $seletect_items must look like <code> (12,12,12)</code)
	 */
	public function setSelectedCartItemsAsOrdered($seletect_items) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update
(
   mla_purchase_cart
)
set  mla_purchase_cart.status  = 'ORDERED'
where 1
and mla_purchase_cart.id  IN " . $seletect_items;
	
		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	
	/**
	 * Add all cart items as pr items.
	 * 
	 * @param unknown $user_id
	 * @param unknown $pr_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function submitCartItems($user_id, $pr_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
insert into mla_purchase_request_items
(
	mla_purchase_request_items.purchase_request_id,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name,
    mla_purchase_request_items.EDT,
	mla_purchase_request_items.unit,
	mla_purchase_request_items.quantity,
    mla_purchase_request_items.article_id,
    mla_purchase_request_items.sparepart_id,
    mla_purchase_request_items.asset_id,
    mla_purchase_request_items.other_res_id,
    mla_purchase_request_items.remarks,    
    mla_purchase_request_items.created_on,
    mla_purchase_request_items.created_by
)
select
	".$pr_id.",
	mla_purchase_cart.priority,
    mla_purchase_cart.name,
    mla_purchase_cart.EDT,
	mla_purchase_cart.unit,
	mla_purchase_cart.quantity,
    mla_purchase_cart.article_id,
    mla_purchase_cart.sparepart_id,
    mla_purchase_cart.asset_id,
    mla_purchase_cart.other_res_id,
    mla_purchase_cart.remarks,    
    mla_purchase_cart.created_on,
    mla_purchase_cart.created_by
from mla_purchase_cart
where 1
and mla_purchase_cart.status is null
and mla_purchase_cart.created_by = " . $user_id;
	
		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * Add selected cart items as pr items.
	 *
	 * @param unknown $user_id
	 * @param unknown $pr_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function submitSelectedCartItems($seletect_items, $pr_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
insert into mla_purchase_request_items
(
	mla_purchase_request_items.purchase_request_id,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name,
    mla_purchase_request_items.EDT,
	mla_purchase_request_items.unit,			
	mla_purchase_request_items.quantity,
    mla_purchase_request_items.article_id,
    mla_purchase_request_items.sparepart_id,
    mla_purchase_request_items.asset_id,
    mla_purchase_request_items.other_res_id,
    mla_purchase_request_items.remarks,
    mla_purchase_request_items.created_on,
    mla_purchase_request_items.created_by
)
select
	".$pr_id.",
	mla_purchase_cart.priority,
    mla_purchase_cart.name,
    mla_purchase_cart.EDT,
	mla_purchase_cart.unit,
	mla_purchase_cart.quantity,
    mla_purchase_cart.article_id,
    mla_purchase_cart.sparepart_id,
    mla_purchase_cart.asset_id,
    mla_purchase_cart.other_res_id,
    mla_purchase_cart.remarks,
    mla_purchase_cart.created_on,
    mla_purchase_cart.created_by
from mla_purchase_cart
where 1
and mla_purchase_cart.id IN " . $seletect_items;
	
		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
}