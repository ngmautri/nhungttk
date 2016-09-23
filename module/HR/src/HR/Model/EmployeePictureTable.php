<?php

namespace HR\Model;

use Zend\Db\TableGateway\TableGateway;
use HR\Model\EmployeePicture;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;


class EmployeePictureTable {
	
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
	 * @param SparepartPicture $input
	 * @return number
	 */
	
	public function add(EmployeePicture $input) {
		$data = array (
				'employee_id' => $input->employee_id,
				'url' => $input->url,
				'filetype' => $input->filetype,
				'size' => $input->size,
				'visibility' => $input->visibility,
				'comments' => $input->comments,
				'uploaded_on' => date ( 'Y-m-d H:i:s' ),
				'filename' => $input->filename,
				'folder' => $input->folder,
				'checksum' => $input->checksum,
		);
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param SparepartPicture $input
	 * @param unknown $id
	 */
	public function update(SparepartPicture $input, $id) {
		$data = array (
				'sparepart_id' => $input->sparepart_id,
				'url' => $input->url,
				'filetype' => $input->filetype,
				'size' => $input->size,
				'visibility' => $input->visibility,
				'comments' => $input->comments,
				'uploaded_on' => date ( 'Y-m-d H:i:s' ),
				'filename' => $input->filename,
				'folder' => $input->folder,
				'checksum' => $input->checksum,
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	
	public function getSparepartPicturesById($id)
	{		
		$id  = (int) $id;
		
		$where = 'sparepart_id = ' . $id;
		$rowset = $this->tableGateway->select($where);
		return $rowset;
	}
	
	
	public function getSPPicturesById($id)
	{
	
		$sql ="
select 
*
from mla_sparepart_pics
where 1 AND mla_sparepart_pics.sparepart_id = " . $id;
				
		$sql = $sql . " Order by mla_sparepart_pics.uploaded_on DESC"; 
			
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	public function isChecksumExits($id, $checksum)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'employee_id=?'		=>$id,
				'checksum=?'		=>$checksum,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'hr_employee_picture'));
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