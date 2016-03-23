<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\ParameterContainer;

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
	
	public function getSpareparts() {
	
		$adapter = $this->tableGateway->adapter;
		
		$sql = "select lt1.*, tIN.totalINFLOW, tOUT.totalOUTFLOW from mla_spareparts as lt1 left join
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.id

left join
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.id
		";
		
		$statement = $adapter->query($sql);
		$result = $statement->execute();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;	
		
	}
	
	public function getLimitedSpareparts($limit, $offset) {
	
		$adapter = $this->tableGateway->adapter;
	
		$sql = "select lt1.*, tIN.totalINFLOW, tOUT.totalOUTFLOW from mla_spareparts as lt1 left join
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.id
	
left join
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.id
		limit " . $limit . ' offset '. $offset ;
	
		
		
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
		$this->tableGateway->update( $data,$where);
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	public function isTagExits($tag)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'tag=?'		=>$tag,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_spareparts'));
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