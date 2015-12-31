<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetPicture;	


class AssetPictureTable {
	
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
		
		$where = 'id = ' . $id;
		$rowset = $this->tableGateway->select($where);
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	
	public function add(AssetPicture $input) {
		$data = array (
				'asset_id' => $input->asset_id,
				'url' => $input->url,
				'filetype' => $input->filetype,
				'size' => $input->size,
				'visibility' => $input->visibility,
				'comments' => $input->comments,
				'uploaded_on' => date ( 'Y-m-d H:i:s' ) 
		);
		$resultSet = $this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(AssetPicture $input, $id) {
		$data = array (
				'asset_id' => $input->asset_id,
				'url' => $input->url,
				'filetype' => $input->filetype,
				'size' => $input->size,
				'visibility' => $input->visibility,
				'comments' => $input->comments,
				'uploaded_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$where = 'id = ' . $id;
		$resultSet = $this->tableGateway->update( $data,$where);
	}
	
	public function getAssetPicturesById($id)
	{		
		$id  = (int) $id;
		
		$where = 'asset_id = ' . $id;
		$rowset = $this->tableGateway->select($where);
		return $rowset;
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
}