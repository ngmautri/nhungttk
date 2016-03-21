<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetPicture;	
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;


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
				'uploaded_on' => date ( 'Y-m-d H:i:s' ),
				'filename' => $input->filename,
				'folder' => $input->folder,
				'checksum' => $input->checksum,
				
		);
		$this->tableGateway->insert ( $data );
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
				'uploaded_on' => date ( 'Y-m-d H:i:s' ),
				'filename' => $input->filename,
				'folder' => $input->folder,
				'checksum' => $input->checksum,
				
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
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
	
	public function isChecksumExits($id, $checksum)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'asset_id=?'		=>$id,
				'checksum=?'		=>$checksum,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_asset_pics'));
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