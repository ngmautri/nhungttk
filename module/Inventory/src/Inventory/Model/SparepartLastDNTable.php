<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\SparepartLastDN;

class SparepartLastDNTable {
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
	
	
	/**
	 * 
	 * @param ArticleLastDN $input
	 * @return number
	 */
	public function add(SparepartLastDN $input) {
		
		$result = $this->getSparepartID($input->sparepart_id);
		
		if($result !== null):
			$this->update($input, $result->id);
			return $result->id;
		else:
			$data = array (
					'sparepart_id' => $input->sparepart_id,
					'last_workflow_id' => $input->last_workflow_id,
			);
			$this->tableGateway->insert ( $data );
			return $this->tableGateway->lastInsertValue;
		endif;
	}
	
	/**
	 * 
	 * @param ArticleLastDN $input
	 * @param unknown $id
	 */
	public function update(SparepartLastDN $input, $id) {
		$data = array (
				'sparepart_id' => $input->sparepart_id,
				'last_workflow_id' => $input->last_workflow_id,
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
	 */
	public function getSparepartID($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "
SELECT * from mla_spareparts_last_dn as TT1
WHERE TT1.sparepart_id =". $id;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if(count($resultSet) >0 ):
			return $resultSet->current();
		else:
			return null;
		endif;
	}
}