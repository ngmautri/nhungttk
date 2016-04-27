<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;


use Inventory\Model\SparepartCategoryMember;


class ArticleCategoryMemberTable {
	
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
	
	public function getMembersByCatIdWithBalance($id)
	{
	
	$sql = "SELECT tSP.*, tIN.totalINFLOW, tOUT.totalOUTFLOW FROM mla_sparepart_cats_members as lt1 LEFT JOIN
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.sparepart_id
LEFT JOIN
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.sparepart_id
LEFT JOIN mla_spareparts as tSP on tSP.id = lt1.sparepart_id				
WHERE lt1.sparepart_cat_id ='". $id ."'";
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
	
		//$container =  new ParameterContainer();
		//$container->offsetSet('limit', $limit, $container::TYPE_INTEGER);
		//$container->offsetSet('offset', $offset, $container::TYPE_INTEGER);
	
		//$parameters = array((int)$limit,(int)$offset);
	
		// bug with quoting LIMIT and OFFSET
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	
	}
	
	
	public function getLimitMembersByCatId($id,$limit,$offset)
	{
		$id  = (int) $id;
	
		$adapter = $this->tableGateway->adapter;
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$where = 'sparepart_cat_id ='.$id;
			
		$select->from(array('t1'=>'mla_sparepart_cats_members'));
		$select->join(array('t2' => 'mla_spareparts'), 't2.id = t1.sparepart_id','*', \Zend\Db\Sql\Select::JOIN_INNER);
		$select->where($where)->limit($limit)->offset($offset);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		return $results;
	}
	
	public function getLimitMembersByCatIdWithBalance($id,$limit,$offset)
	{
		
		$sql = "SELECT tSP.*, tIN.totalINFLOW, tOUT.totalOUTFLOW FROM mla_sparepart_cats_members as lt1 LEFT JOIN
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.sparepart_id
LEFT JOIN
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.sparepart_id
LEFT JOIN mla_spareparts as tSP on tSP.id = lt1.sparepart_id				
WHERE lt1.sparepart_cat_id ='". $id ."' limit " . $limit . ' offset '. $offset ;
	
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
		
		
		//$container =  new ParameterContainer();
		//$container->offsetSet('limit', $limit, $container::TYPE_INTEGER);
		//$container->offsetSet('offset', $offset, $container::TYPE_INTEGER);
				
		//$parameters = array((int)$limit,(int)$offset);
		
		// bug with quoting LIMIT and OFFSET
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	
	}
	
	
	/*
	SELECT * FROM `mla_spareparts` as t1 WHERE t1.id NOT IN 
	(SELECT t2.sparepart_id FROM `mla_sparepart_cats_members` as t2 
		INNER JOIN mla_spareparts as t3 on t3.id = t2.sparepart_id WHERE t2.sparepart_cat_id =2)
	
	*/
	
	public function getNoneMembersOfCatId($id)
	{
		$id  = (int) $id;
	
		$adapter = $this->tableGateway->adapter;
	
		$sql = new Sql($adapter);
		$select = $sql->select();
		
	
		$where = 't1.sparepart_cat_id ='.$id;
			
		$select->from(array('t1'=>'mla_sparepart_cats_members'));
		$select->columns(array('sparepart_id'));
		$select->join(array('t2' => 'mla_spareparts'), 't2.id = t1.sparepart_id',array(), \Zend\Db\Sql\Select::JOIN_INNER);
		$select->where($where);
		
		//$selectSql = '('.$sql->getSqlStringForSqlObject($select).')';
				
		$w =  new Where();
		$w->literal('t3.id NOT IN (' . $sql->getSqlStringForSqlObject($select) .')');
				
		$select1 = $sql->select();
		$select1->from(array('t3'=>'mla_spareparts'));
		$select1->where($w);
		
		//var_dump($subquery);
			
		//var_dump($select1->getSqlString());
		$statement = $sql->prepareStatementForSqlObject($select1);
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