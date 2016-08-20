<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;


use Inventory\Model\SparepartCategoryMember;


class SparepartCategoryMemberTable {
	
	private $getSP_SQL ="
	select
	*
	from 
	(
		select 
			mla_spareparts.*,
			ifnull(mla_spareparts_inflow.total_inflow,0) as total_inflow,
			ifnull(mla_spareparts_outflow.total_outflow,0) as total_outflow,
            (mla_spareparts_inflow.total_inflow - mla_spareparts_outflow.total_outflow) as current_balance,
			ifnull(mla_sparepart_minimum_balance.minimum_balance,0) as mininum_balance
			
		from mla_spareparts

		/* inflow*/
		left join
		(
			select  
			mla_spareparts.id, 
				SUM(mla_sparepart_movements.quantity) AS total_inflow
			from mla_spareparts 
			LEFT JOIN mla_sparepart_movements 
			on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
			where mla_sparepart_movements.flow = 'IN' 
			GROUP BY mla_sparepart_movements.sparepart_id
		) as mla_spareparts_inflow
		on mla_spareparts_inflow.id = mla_spareparts.id

		/* outflow*/
		left join
		(
			select  
			mla_spareparts.id, 
				SUM(mla_sparepart_movements.quantity) AS total_outflow
			from mla_spareparts 
			LEFT JOIN mla_sparepart_movements 
			on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
			where mla_sparepart_movements.flow = 'OUT' 
			GROUP BY mla_sparepart_movements.sparepart_id
		) 
		as mla_spareparts_outflow
		on mla_spareparts_outflow.id = mla_spareparts.id

		/*minimum_balance*/
		left join mla_sparepart_minimum_balance
		on mla_sparepart_minimum_balance.sparepart_id =  mla_spareparts.id
	)
	as mla_spareparts

	WHERE 1			
			";
	
	private $getSP_Cat_SQL = "
	         select 
				mla_sparepart_cats.id as sparepart_cat_id,
                mla_sparepart_cats.name as cat_name,
				mla_spareparts.*,
                ifnull(mla_spareparts_inflow.total_inflow,0) as total_inflow,
				ifnull(mla_spareparts_outflow.total_outflow,0) as total_outflow,
				(ifnull(mla_spareparts_inflow.total_inflow,0) - ifnull(mla_spareparts_outflow.total_outflow,0)) as current_balance,
				ifnull(mla_sparepart_minimum_balance.minimum_balance,0) as mininum_balance,
				((ifnull(mla_spareparts_inflow.total_inflow,0) - ifnull(mla_spareparts_outflow.total_outflow,0)) - ifnull(mla_sparepart_minimum_balance.minimum_balance,0)) as remaining_to_order,
                mla_sparepart_pics.id as sp_pic_id,
                mla_sparepart_pics.filename,
                mla_sparepart_pics.url,
                mla_sparepart_pics.folder
			from mla_sparepart_cats_members

			/* inflow*/
			left join
			(
				select  
				mla_spareparts.id, 
					SUM(mla_sparepart_movements.quantity) AS total_inflow
				from mla_spareparts 
				LEFT JOIN mla_sparepart_movements 
				on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
				where mla_sparepart_movements.flow = 'IN' 
				GROUP BY mla_sparepart_movements.sparepart_id
			) as mla_spareparts_inflow
			on mla_spareparts_inflow.id = mla_sparepart_cats_members.sparepart_id

			/* outflow*/
			left join
			(
				select  
				mla_spareparts.id, 
				SUM(mla_sparepart_movements.quantity) AS total_outflow
				from mla_spareparts 
				LEFT JOIN mla_sparepart_movements 
				on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
				where mla_sparepart_movements.flow = 'OUT' 
				GROUP BY mla_sparepart_movements.sparepart_id
			) 
			as mla_spareparts_outflow
			on mla_spareparts_outflow.id = mla_sparepart_cats_members.sparepart_id

			/*minimum_balance*/
			left join mla_sparepart_minimum_balance
			on mla_sparepart_minimum_balance.sparepart_id =  mla_sparepart_cats_members.sparepart_id
            
			/*picture*/
            left join 
            (
	            select
				*
				from 
                (
                select * from
					mla_sparepart_pics
                    order by mla_sparepart_pics.uploaded_on desc
                )
                as mla_sparepart_pics
				group by mla_sparepart_pics.sparepart_id
            )
            as mla_sparepart_pics
            on mla_sparepart_pics.sparepart_id = mla_sparepart_cats_members.sparepart_id

			/*SP*/
            left join mla_spareparts
            on mla_spareparts.id = mla_sparepart_cats_members.sparepart_id
            
			/* sp_cat*/
            left join mla_sparepart_cats
            on mla_sparepart_cats.id = mla_sparepart_cats_members.sparepart_cat_id
            WHERE 1
	";
	
	private $getAllSP_SQL ="
				           select 
				mla_sparepart_cats.sparepart_cat_id as sparepart_cat_id,
                mla_sparepart_cats.name as cat_name,
				mla_spareparts.*,
                ifnull(mla_spareparts_inflow.total_inflow,0) as total_inflow,
				ifnull(mla_spareparts_outflow.total_outflow,0) as total_outflow,
				(ifnull(mla_spareparts_inflow.total_inflow,0) - ifnull(mla_spareparts_outflow.total_outflow,0)) as current_balance,
				ifnull(mla_sparepart_minimum_balance.minimum_balance,0) as minimum_balance,
				((ifnull(mla_spareparts_inflow.total_inflow,0) - ifnull(mla_spareparts_outflow.total_outflow,0)) - ifnull(mla_sparepart_minimum_balance.minimum_balance,0)) as remaining_to_order,
                mla_sparepart_pics.id as sp_pic_id,
                mla_sparepart_pics.filename,
                mla_sparepart_pics.url,
                mla_sparepart_pics.folder
			from mla_spareparts

			/* inflow*/
			left join
			(
				select  
					mla_spareparts.id, 
					SUM(mla_sparepart_movements.quantity) AS total_inflow
				from mla_spareparts 
				LEFT JOIN mla_sparepart_movements 
				on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
				where mla_sparepart_movements.flow = 'IN' 
				GROUP BY mla_sparepart_movements.sparepart_id
			) as mla_spareparts_inflow
			on mla_spareparts_inflow.id = mla_spareparts.id

			/* outflow*/
			left join
			(
				select  
				mla_spareparts.id, 
				SUM(mla_sparepart_movements.quantity) AS total_outflow
				from mla_spareparts 
				LEFT JOIN mla_sparepart_movements 
				on mla_sparepart_movements.sparepart_id = mla_spareparts.id 
				where mla_sparepart_movements.flow = 'OUT' 
				GROUP BY mla_sparepart_movements.sparepart_id
			) 
			as mla_spareparts_outflow
			on mla_spareparts_outflow.id = mla_spareparts.id

			/*minimum_balance*/
			left join mla_sparepart_minimum_balance
			on mla_sparepart_minimum_balance.sparepart_id =  mla_spareparts.id
            
			/*picture*/
            left join 
            (
	            select
				*
				from 
                (
                select * from
					mla_sparepart_pics
                    order by mla_sparepart_pics.uploaded_on desc
                )
                as mla_sparepart_pics
				group by mla_sparepart_pics.sparepart_id
            )
            as mla_sparepart_pics
            on mla_sparepart_pics.sparepart_id = mla_spareparts.id

            
			/* sp_cat*/
           left join 
		   (
			   select
				mla_sparepart_cats.name,
				mla_sparepart_cats_members.sparepart_id,
				mla_sparepart_cats_members.sparepart_cat_id
				from mla_sparepart_cats
				left join mla_sparepart_cats_members
				on mla_sparepart_cats_members.sparepart_cat_id = mla_sparepart_cats.id
			)
            as mla_sparepart_cats
            
            on mla_sparepart_cats.sparepart_id = mla_spareparts.id
            WHERE 1
 			";
	
	
	private $getTotalCatMembers_SQL = "
		select 
		count(*) as total_members
		from mla_sparepart_cats_members
		where 1 	
	";
	
	private $getTotalSP_SQL = "
		select 
		count(*) as total_sp
		from mla_spareparts
		where 1 	
	";
	
	private $getTotalSPHavingMinBalance_SQL = "
			select 
				count(*)as total_sp
			from mla_spareparts

			/*minimum_balance*/
			left join mla_sparepart_minimum_balance
			on mla_sparepart_minimum_balance.sparepart_id =  mla_spareparts.id
            
		    WHERE 1
            and mla_sparepart_minimum_balance.minimum_balance >0
			
			";
	
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
	
	
	/**
	 * 
	 * @param unknown $id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	
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
	
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
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
	
	/**
	 *
	 * @param unknown $id
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getTotalMembersOfCatID($id)
	{
	
		$sql = $this->getTotalCatMembers_SQL;
		
		if($id>0){
			$sql = $sql. " AND mla_sparepart_cats_members.sparepart_cat_id =" .$id;
		}
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return (int)$resultSet->current()->total_members;
	}
	
	/**
	 *
	 * @param unknown $id
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getTotalSP()
	{
	
		$sql = $this->getTotalSP_SQL;
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return (int)$resultSet->current()->total_sp;
	}
	
	
	/**
	 *
	 * @param unknown $id
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getMembersOfCatID($id,$limit,$offset)
	{
	
		$sql = $this->getSP_Cat_SQL;
		
		if($id>0)
		{
			$sql = $sql. " AND mla_sparepart_cats_members.sparepart_cat_id =" .$id;
		}
		
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getAllSP($limit,$offset)
	{
	
		$sql = $this->getAllSP_SQL;
	
		
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getOrderSuggestion($limit,$offset)
	{
	
		$sql = $this->getAllSP_SQL;
		$sql = $sql . " AND mla_sparepart_minimum_balance.minimum_balance >0";
		$sql = $sql . " Order by ((ifnull(mla_spareparts_inflow.total_inflow,0) - ifnull(mla_spareparts_outflow.total_outflow,0)) - ifnull(mla_sparepart_minimum_balance.minimum_balance,0)) ";
		
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getTotalSPHavingMinBalance()
	{
	
		$sql = $this->getTotalSPHavingMinBalance_SQL;
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return (int)$resultSet->current()->total_sp;
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
	
	
	/**
	 * 
	 * @param SparepartCategoryMember $input
	 * @return number
	 */
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
	
	/**
	 * 
	 * @param unknown $sparepart_id
	 * @param unknown $cat_id
	 * @return boolean
	 */
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