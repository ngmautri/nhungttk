<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\SparepartPicture;	


class SparepartPictureTable {
	
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
	
	public function add(SparepartPicture $input) {
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
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
}