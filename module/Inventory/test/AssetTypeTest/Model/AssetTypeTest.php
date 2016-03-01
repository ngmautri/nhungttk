<?php

namespace AssetTypeTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use Inventory\Model\AssetCategory;
use Inventory\Model\AssetCategoryTable;
use Inventory\Model\AssetGroup;
use Inventory\Model\AssetGroupTable;
use AssetTypeTest\Bootstrap;
use Inventory\Services\AssetService;


use Inventory\Model\SparepartCategory;
use Inventory\Model\SparepartCategoryMember;

class AssetTypeTest extends PHPUnit_Framework_TestCase {
	
	/*
	 * public function testAssetTypeTable() {
	 *
	 *
	 * $resultSet = new ResultSet();
	 * $AssetTypeTable = Bootstrap::getServiceManager()->get('Inventory\Model\AssetTypeTable');
	 * $assetType = new AssetType;
	 * $assetType->type = "Machinery";
	 * $assetType->description = "Machinery";
	 *
	 * $AssetTypeTable->add($assetType);
	 * var_dump($AssetTypeTable->fetchAll());
	 *
	 * $as = Bootstrap::getServiceManager()->get('Inventory\Services\AssetService');
	 * $as->createAssetFolderById ( 7 );
	 * }
	 */
	
	/*
	 * public function testAssetCatergoryTest() {
	 * $resultSet = new ResultSet ();
	 * $AssetCategoryTable = Bootstrap::getServiceManager()->get('Inventory\Model\AssetCategoryTable');
	 *
	 * $assetType = new AssetCategory();
	 * $assetType->category = "Buiding ";
	 * $assetType->description = "Machinery details";
	 *
	 * //$AssetCategoryTable->add($assetType);
	 * echo $AssetCategoryTable->add($assetType);
	 *
	 * }
	 */
	
	/*
	public function testAssetServiceTest() {
		$resultSet = new ResultSet ();
		
 		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\SparepartService' );
		
		for($i = 1; $i <= 917; $i ++) {
			print $sv->createSparepartFolderById ( $i );
	} 
	*/
		
		
 	/* 	$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\AssetService' );
		
		for($i = 1; $i <= 808; $i ++) {
			print $sv->createAssetFolderById ( $i );
		}  
	}
	*/
	
/* 	public function testAssetGroupTest() {
		$resultSet = new ResultSet ();
		$table = Bootstrap::getServiceManager()->get('Inventory\Model\AssetGroupTable');
			
		$assetType = new AssetGroup();
		$assetType->category_id = 4;
		$assetType->name = "Press button machine";
		$assetType->description = "Press button machine";
		
		//$table->add($assetType);
		//$table->update($assetType,1);
		var_dump($table->get(2));
	
	} */
	
	/*
	public function testAssetSearchServieTest() {
		$resultSet = new ResultSet ();
	
		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\AssetSearchService' );
	
		$sv->createIndex();
		
		//$sv->search("00051");
		
	}
	
	*/
	/*
	public function testAssetSearchServieTest() {
		$resultSet = new ResultSet ();
		$sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\SparePartsSearchService' );
		//$sv->createIndex();
		
		//var_dump($sv->search('bobbin'));
		
		$pictures_dir =  ROOT . DIRECTORY_SEPARATOR . "/data" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "asset_22".DIRECTORY_SEPARATOR."pictures".DIRECTORY_SEPARATOR;
	
		$name='0002.png';
		
		var_dump($pictures_dir.$name);
		
		$im = imagecreatefrompng($pictures_dir.$name);
			
		$ox = imagesx($im);
		$oy = imagesy($im);
		
		$final_width_of_image =450;
		
		$nx = $final_width_of_image;
		$ny = floor($oy * ($final_width_of_image / $ox));
			
		$nm = imagecreatetruecolor($nx, $ny);
		
		$name_thumbnail = 'thumbnail_450_'.$name ;
		var_dump($name_thumbnail);
			
		imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
		imagepng($nm, "$pictures_dir/$name_thumbnail");
		
	}
	*/
	
	
	 public function testSparepartCatergoryTest() {
	 /* 
	 $resultSet = new ResultSet ();
	 $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\SparepartCategoryTable');
	 
	 $data = new SparepartCategory();
	 $data->name = "1 needle m/c";
	 $data->description = "1 needle m/c";
	 var_dump($tbl->add($data)); 
	 */
	 	
	 	
	 	 $resultSet = new ResultSet ();
	 	 $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\SparepartCategoryMemberTable');
	 	
	 	 //$data = new SparepartCategoryMember();
	 	 //$data->sparepart_id = 1;
	 	 //$data->sparepart_cat_id=1;
	 	 //var_dump($tbl->add($data)); 
	 	 $result = $tbl->getMembersByCatId(19);
	 	 
		 foreach ($result as $user) {
	        echo $user['name'];
	    }
	 
	 //$table->add($assetType);
	
		//var_dump($tbl->fetchall());
		
	 }

}