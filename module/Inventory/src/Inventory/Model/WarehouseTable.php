<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\Warehouse;

class WarehouseTable {
	protected $tableGateway;
	
	/**
	 * 
	 * @param TableGateway $tableGateway
	 */
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * 
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 */
	public function get($id) {
		$id = ( int ) $id;
		
		$where = 'id = ' . $id;
		$rowset = $this->tableGateway->select ( $where );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/*
	public $id;
	public $wh_code;
	public $wh_name;

	public $wh_address;
	public $wh_country;
	public $wh_contract_person;
	public $wh_telephone;
	public $wh_email;
	public $wh_status;
	
	public $created_on;
	public $created_by;
	 */
	public function add(Warehouse $input) {
		$data = array (
				'wh_code' => $input->wh_code,
				'wh_name' => $input->wh_name,				
				'wh_address' => $input->wh_address,
				'wh_country' => $input->wh_country,
				'wh_contact_person' => $input->wh_contact_person,
				'wh_telephone' => $input->wh_telephone,
				'wh_email' => $input->wh_email,
				'wh_status' => $input->wh_status,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
		);
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param SparepartPicture $input        	
	 * @param unknown $id        	
	 */
	public function update(Warehouse $input, $id) {
		$data = array (
				'wh_code' => $input->wh_code,
				'wh_name' => $input->wh_name,
				'wh_address' => $input->wh_address,
				'wh_country' => $input->wh_country,
				'wh_contract_person' => $input->wh_contact_person,
				'wh_telephone' => $input->wh_telephone,
				'wh_email' => $input->wh_email,
				'wh_status' => $input->wh_status,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
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
	 * @param unknown $id
	 * @param unknown $checksum
	 * @return boolean
	 */
	public function isWHCodeExits($wh_code)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'wh_code=?'		=>$wh_code,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'nmt_inventory_warehouse'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 *
	 * @param unknown User $id
	 */
	public function getPurchasingDataOf($id)
	{
		$sql ="select 
	mla_articles_purchasing.*,
	mla_vendors.name as vendor_name,
	mla_articles.name as article_name,
    mla_articles.code as article_code
    
from mla_articles_purchasing
join mla_vendors
on mla_vendors.id = mla_articles_purchasing.vendor_id
join mla_articles
on mla_articles.id = mla_articles_purchasing.article_id Where 1";
		
		$sql = $sql. " AND mla_articles_purchasing.article_id=".$id;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
}