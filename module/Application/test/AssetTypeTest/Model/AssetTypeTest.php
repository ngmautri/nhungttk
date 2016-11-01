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
		
	 	 $sv = Bootstrap::getServiceManager()->get('Application\Model\CurrencyTable');
	 	 //$sv->createPdf();
	 	 //echo base64_encode('1234THMMLA-'.'15'.'-POndsfdsy');
	 	 //echo base64_decode('MTIzNFRITU1MQS0xNS1QT25kc2Zkc3k');
	 	 var_dump($sv->fetchALL());
	 	 
	 	 /*
	 	 $c = $sv->getControllerManager();
	 	 foreach($c->getRegisteredServices() as $a){
	 	 	var_dump($a);
	 	 }
	 	 */
	 	
	 }

}