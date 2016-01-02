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
	public function testAssetCatergoryTest() {
		$resultSet = new ResultSet ();
		$AssetCategoryTable = Bootstrap::getServiceManager()->get('Inventory\Model\AssetCategoryTable');
		 
		 $assetType = new AssetCategory();
		 $assetType->category = "Buiding	";
		 $assetType->description = "Machinery details";
		 
		 //$AssetCategoryTable->add($assetType);
		 echo $AssetCategoryTable->add($assetType);
	
	}*/
	
	
	public function testAssetServiceTest() {
		$resultSet = new ResultSet ();
		$sv = Bootstrap::getServiceManager()->get('Inventory\Services\SparepartService');
			
		/*for ($i=1;$i<=808;$i++) {
			print $sv->createAssetFolderById($i);
		}
		*/
		
		print $sv->createSparepartFolderById(1);
		var_dump($sv->getSparepartPath(1));
	
	}
	
	
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

}