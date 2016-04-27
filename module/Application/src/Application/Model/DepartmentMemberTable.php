<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;


use Application\Model\DepartmentMember;


class DepartmentMemberTable {
	
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
	
	
	public function getLimitMembersByDepartmentID($id,$limit,$offset)
	{
		$id  = (int) $id;
	
		$adapter = $this->tableGateway->adapter;
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$where = 'department_id ='.$id;
			
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
	
	public function getNoneMembersOfDepartment($id)
	{
		$id  = (int) $id;
	
		$adapter = $this->tableGateway->adapter;
	
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 't1.department_id ='.$id;
			
		$select->from(array('t1'=>'mla_departments_members'));
		$select->columns(array('user_id'));
		$select->join(array('t2' => 'mla_users'), 't2.id = t1.user_id',array(), \Zend\Db\Sql\Select::JOIN_INNER);
		$select->where($where);
		
		//$selectSql = '('.$sql->getSqlStringForSqlObject($select).')';
				
		$w =  new Where();
		$w->literal('t3.id NOT IN (' . $sql->getSqlStringForSqlObject($select) .')');
				
		$select1 = $sql->select();
		$select1->from(array('t3'=>'mla_users'));
		$select1->where($w);
		
		//var_dump($subquery);
			
		//var_dump($select1->getSqlString());
		$statement = $sql->prepareStatementForSqlObject($select1);
		$results = $statement->execute();
	
		return $results;
	}
	
	
	
	/**
	 * 
	 * @param DepartmentMember $input
	 */
	public function add(DepartmentMember $input) {
		$data = array (
				'department_id' => $input->department_id,
				'user_id' => $input->user_id,
				'updated_by' => $input->updated_by,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param DepartmentMember $input
	 * @param unknown $id
	 */
	public function update(DepartmentMember $input, $id) {
			$data = array (
				'department_id' => $input->department_id,
				'user_id' => $input->user_id,
				'updated_by' => $input->updated_by,
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 * @author: Nguyen Mau Tri
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	/**
	 * 
	 * @param unknown $user_id
	 * @param unknown $department_id
	 * @return boolean
	 */
	public function isMember($user_id, $department_id)
	{
			$adapter = $this->tableGateway->adapter;
		
		$where = array(
				'user_id=?'		=> $user_id,
				'department_id=?' 	=> $department_id
		);
		
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$select->from(array('t1'=>'mla_departments_members'));
		$select->where($where);

		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function getMembersOf($id)
	{
	
		$sql = 
		"SELECT t1.*, t2.firstname, t2.lastname, t2.email, t3.name as department_name from mla_departments_members as t1
left join mla_users as t2
on t1.user_id = t2.id
left join mla_departments as t3
on t1.department_id = t3.id
where t1.department_id ='". $id ."'";
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	public function getLimitMembersOf($id,$limit,$offset)
	{
	
		$sql =
		"SELECT t1.*, t2.firstname, t2.lastname, t2.email, t3.name as department_name from mla_departments_members as t1
left join mla_users as t2
on t1.user_id = t2.id
left join mla_departments as t3
on t1.department_id = t3.id
WHERE t1.department_id ='". $id ."' limit " . $limit . ' offset '. $offset ;
				
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
}