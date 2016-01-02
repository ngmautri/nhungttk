<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

use Inventory\Model\SparepartMovement;	


class SparepartMovementsTable {
	
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
	 * @param unknown $id
	 * @throws \Exception
	 */
	public function get($id){
		
		$id  = (int) $id;
		
		$where = 'id = ' . $id;
		$rowset = $this->tableGateway->select($where);
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	/**
	 * 
	 * @param SparepartMovement $input
	 */	
	public function add(SparepartMovement $input) {
		$data = array (
				'movement_date' => $input->movement_date,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'flow' => $input->flow,
				'quantity' => $input->quantity,
				'reason' => $input->reason,
				'requester' => $input->requester,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		$resultSet = $this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param SparepartPicture $input
	 * @param unknown $id
	 */
	public function update(SparepartMovement $input, $id) {
		$data = array (
				'movement_date' => $input->movement_date,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'flow' => $input->flow,
				'quantity' => $input->quantity,
				'reason' => $input->reason,
				'requester' => $input->requester,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' )  
		);
		
		$where = 'id = ' . $id;
		$resultSet = $this->tableGateway->update( $data,$where);
	}
			
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function getMovementsOfSparepartByID($id){
		
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 'sparepart_id ='.$id;
			
		$select->from('mla_sparepart_movements');
		$select->where($where);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return $results;
	}
	
	public function getTotalInflowOf($id){
		
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 'sparepart_id ='.$id.' AND flow = "IN" ';
		
		$select->from('mla_sparepart_movements');
		$select->where($where);
		$select->columns(array(new \Zend\Db\Sql\Expression('SUM(quantity) as inflow')));
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
	if ($results->count() > 0) {
			 $r =  $results->current();
			 return (int) $r['inflow'];
			
		}else {
			return 0;
		}
	}
	
	public function getTotalOutflowOf($id){
	
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 'sparepart_id ='.$id.' AND flow = "OUT" ';
		
		$select->from('mla_sparepart_movements');
		$select->where($where);
		$select->columns(array(new \Zend\Db\Sql\Expression('SUM(quantity) as outflow')));
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		if ($results->count() > 0) {
			 $r =  $results->current();
			 return (int) $r['outflow'];
			
		}else {
			return 0;
		}
				
	
		
	}
	
}