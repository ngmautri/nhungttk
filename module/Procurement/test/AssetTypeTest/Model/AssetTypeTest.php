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
	
	
	 public function testDBTest() {
	 	 $tbl = Bootstrap::getServiceManager()->get('Procurement\Model\PurchaseRequestItemPicTable');
	 	 
	  	 $result = $tbl->fetchAll();
	  	 
	  	var_dump($result);
		
	  	 /*
	  	 foreach ($result as $user) {
	 	 	var_dump ($user);
	  	 }
	  	 */	
		
	 }

}