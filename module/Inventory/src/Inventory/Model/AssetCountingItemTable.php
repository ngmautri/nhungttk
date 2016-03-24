<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetCountingItem;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;


class AssetCountingItemTable {
	
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
	$sql = '(SELECT t1.*, t2.name as asset_name, t2.tag as tag
		FROM mla_asset_counting_items as t1
		LEFT JOIN mla_asset AS t2
		ON t2.id = t1.asset_id
		WHERE t1.counting_id = '. $id) as T2;
	*/
	public function getCountedItems($id){
	
		$adapter = $this->tableGateway->adapter;
		
		
		$sql = 'SELECT TBL1.*,TBL2.*
		FROM mla_asset as TBL1
		LEFT JOIN (SELECT t1.*
		FROM mla_asset_counting_items as t1
		left JOIN mla_asset AS t2
		ON t2.id = t1.asset_id
		WHERE t1.counting_id = '. $id. ') as TBL2				
		ON TBL1.id = TBL2.asset_id
		ORDER BY TBL2.counted_on DESC';
		
		$statement = $adapter->query($sql);
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	
	public function add(AssetCountingItem $input) {
		$data = array (
				'counting_id' => $input->counting_id,
				'asset_id' => $input->asset_id,
				'location' => $input->location,
				'counted_by' => $input->counted_by,
				'verified_by' => $input->verified_by,
				'acknowledged_by' => $input->acknowledged_by,
				'counted_on' => date ( 'Y-m-d H:i:s' ) 
		);
		 $this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(AssetCountingItem $input, $id) {
		$data = array (
				'counting_id' => $input->counting_id,
				'asset_id' => $input->asset_id,
				'location' => $input->location,
				'counted_by' => $input->counted_by,
				'verified_by' => $input->verified_by,
				'acknowledged_by' => $input->acknowledged_by,
				'counted_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	public function isAssetCounted($counting_id, $asset_id)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'asset_id=?'		=>$asset_id,
				'counting_id=?'		=>$counting_id,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_asset_counting_items'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
}