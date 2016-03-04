<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

use Inventory\Model\SparepartCategoryMember;


class SparepartCategoryMemberTable {
	
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
	 *	 SELECT t2.name as l1, t3.name as lev3
		 FROM mib_branches AS t1
		 LEFT JOIN mib_branches AS t2 ON t2.parent_id = t1.id
		 LEFT JOIN mib_branches AS t3 ON t3.parent_id = t2.id
		 WHERE t1.name = '_ROOT_'
	 * 
	 */
	public function getMembersByCatId($id)
	{
		$id  = (int) $id;
	
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 'sparepart_cat_id ='.$id;
			
		$select->from(array('t1'=>'mla_sparepart_cats_members'));
		$select->join(array('t2' => 'mla_spareparts'), 't2.id = t1.sparepart_id','*', \Zend\Db\Sql\Select::JOIN_INNER);
		$select->where($where);

		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return $results;
	}
	
	
	public function add(SparepartCategoryMember $input) {
		$data = array (
				'sparepart_id' => $input->sparepart_id,
				'sparepart_cat_id' => $input->sparepart_cat_id,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(SparepartCategoryMember $input, $id) {
		$data = array (
				'sparepart_id' => $input->sparepart_id,
				'sparepart_cat_id' => $input->sparepart_cat_id,
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	public function isMember($sparepart_id, $cat_id)
	{
			$adapter = $this->tableGateway->adapter;
		
		$where = array(
				'sparepart_id=?'		=> $sparepart_id,
				'sparepart_cat_id=?' 	=> $cat_id
		);
		
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$select->from(array('t1'=>'mla_sparepart_cats_members'));
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