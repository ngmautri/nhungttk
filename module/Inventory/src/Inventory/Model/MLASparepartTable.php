<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

use Inventory\Model\MLASparepart;

/**
 * 
 * @author nmt
 *
 */
class MLASparepartTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	
	/**
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	
	public function fetchAll() {
		
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('mla_spareparts');
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		// array
		return $results;
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @throws \Exception
	 */
	public function get($id){
	
		$id  = (int) $id;
	
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	public function getLimitSpareParts($limit,$offset){
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
				
		$select->from('mla_spareparts');
		$select->limit($limit)->offset($offset);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		// array
		return $results;
	}
		
	/**
	 * 
	 * @param MLASparepart $input
	 */
	public function add(MLASparepart $input) {
		$data = array (
				'name' => $input->name,
				'name_local' => $input->name_local,
				'description' => $input->description,				
				'code' => $input->code,				
				'tag' => $input->tag,
				'location' => $input->location,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param MLAAsset $input
	 * @param unknown $id
	 */
	public function update(MLASparepart $input, $id) {
		
		$data = array (
				'name' => $input->name,
				'name_local' => $input->name_local,
				'description' => $input->description,				
				'code' => $input->code,				
				'tag' => $input->tag,
				'location' => $input->location,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$where = 'id = ' . $id;
		$resultSet = $this->tableGateway->update( $data,$where);
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}

}