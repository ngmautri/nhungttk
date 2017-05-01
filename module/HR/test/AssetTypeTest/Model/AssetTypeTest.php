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

use Procurement\Model\PRItemWorkFlow;

use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;
use Procurement\Model\PurchaseRequest;

class AssetTypeTest extends PHPUnit_Framework_TestCase {
	
	
	 public function testDBTest() {
	 	 //$tbl = Bootstrap::getServiceManager()->get('Procurement\Model\PRItemSelfConfirmationTable');
	 	 /*
	 	 $input = new PRItemWorkFlow();
	 	 $input->status = "Notified";
	 	 $input->pr_item_id= 1;
	 	 $input->updated_by = 39;
	 	 $tbl->add($input);
	 	  	 
	 	 
	  	 $result = $tbl->updateLastWorkFlow(25,6);
	  	 var_dump ($result);
	  	 /*
	   	 foreach ($result as $user) {
	 	 	var_dump ($user);
	  	 }
	  	 */
	
	 	 //$result = $tbl->getPRItems(2,null,1,0,0);
	 	 
	 	  //$result = $tbl->fetchAll();
	 	  //var_dump($result);
	 	
	 	  /*
	 	 foreach ($result as$user) {
	 	 	var_dump ($user);
	 	 }
	 	 */
	 	
	 	/*
	 	$filename =ROOT . '\nmt.csv';
	 	
	 	$rtn = array();
	 	
	 	if (($handle = fopen($filename, "r")) !== FALSE) {
	 		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	 			$item = array();
	 			$item[] = $data[0];
	 			$item[] = $data[1];
	 			$rtn[] = $item;
	 		}
	 	}
	 	var_dump($rtn);
	 	*/
	 	
	 //	$sv = Bootstrap::getServiceManager ()->get ( 'HR\Model\EmployeePictureTable' );
	 	$sv = Bootstrap::getServiceManager ()->get ( 'HR\Services\EmployeeSearchService' );
	 	
	 	var_dump($sv->updateIndexOfEmployee('0005'));
	 	 
	 }

}