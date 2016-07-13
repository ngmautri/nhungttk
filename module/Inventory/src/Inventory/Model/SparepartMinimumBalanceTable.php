<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet;
use Inventory\Model\SparepartMinimumBalance;

/**
 *
 * @author nmt
 *        
 */
class SparepartMinimumBalanceTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	public function get($id) {
		$id = ( int ) $id;
		
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/**
	 *
	 * @param SparepartMinimumBalance $input        	
	 * @return number
	 */
	public function add(SparepartMinimumBalance $input) {
		$result = $this->getSparepartWithMinBalace ( $input->sparepart_id );
		
		if ($result !== null) :
			$this->update ( $input, $result->id );
			return $result->id;
		 else :
			$data = array (
					'sparepart_id' => $input->sparepart_id,
					'minimum_balance' => $input->minimum_balance,
					'remarks' => $input->remarks,
					'created_by' => $input->created_by,
					'created_on' => date ( 'Y-m-d H:i:s' ),
					'status' => $input->status 
			);
			$this->tableGateway->insert ( $data );
			return $this->tableGateway->lastInsertValue;
		endif;
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param SparepartMinimumBalance $input
	 * @param unknown $id
	 */
	public function update(SparepartMinimumBalance $input, $id) {
		$data = array (
				'sparepart_id' => $input->sparepart_id,
				'minimum_balance' => $input->minimum_balance,
				
				'remarks' => $input->remarks,
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
	 * @param unknown $id
	 * @return ArrayObject|NULL|NULL
	 */
	public function getSparepartID($id) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "
SELECT 
	mla_spareparts.*,
    ifnull(mla_sparepart_minimum_balance.minimum_balance,0) as minimum_balance,
	mla_sparepart_minimum_balance.status
from mla_spareparts
left join  mla_sparepart_minimum_balance
on mla_spareparts.id = mla_sparepart_minimum_balance.sparepart_id
where 1 ";
		$sql = $sql . " AND  mla_spareparts.id = ".$id;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if (count ( $resultSet ) > 0) :
			return $resultSet->current ();
		 else :
			return null;
		endif;
	}
	
	/**
	 *
	 * @param unknown $id
	 * @return ArrayObject|NULL|NULL
	 */
	public function getSparepartWithMinBalace($id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
SELECT * from mla_sparepart_minimum_balance
WHERE mla_sparepart_minimum_balance.sparepart_id = ".$id;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
	
		if (count ( $resultSet ) > 0) :
		return $resultSet->current ();
		else :
		return null;
		endif;
	}
}