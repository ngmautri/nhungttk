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
use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;

use Inventory\Model\SparepartLastDN;
use Inventory\Model\SparepartLastDNTable;

use Inventory\Services\EpartnerService;

class AssetTypeTest extends PHPUnit_Framework_TestCase {
	
	
	 public function testSparepartCatergoryTest() {
		
	 	 $sv = Bootstrap::getServiceManager()->get('Application\Service\ApplicationService');
	 	 $loadedModules = $sv->getLoadedModules();
	 	var_dump($loadedModules);
	 	 
	 	 
	 	 /*
	 	 $c = $sv->getControllerManager();
	 	 //var_dump($c);
	 	 foreach($c->getRegisteredServices() as $a){
	 	 	var_dump($a);
	 	 }
	 	 */
	 	 var_dump($sv->getResources());
	 }

}