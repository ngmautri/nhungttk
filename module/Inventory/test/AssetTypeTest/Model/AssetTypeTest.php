<?php
namespace AssetTypeTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;

use Inventory\Model\AssetTypeTable;
use Inventory\Model\AssetType;
use AssetTypeTest\Bootstrap;

class AssetTypeTest extends PHPUnit_Framework_TestCase{
		private $assetType;

    	public function testAssetTypeTable (){

        $resultSet = new ResultSet();
        $AssetTypeTable  =  Bootstrap::getServiceManager()->get('Inventory\Model\AssetTypeTable');
        
        $assetType = new AssetType;
        $assetType->type = "Machinery";
        $assetType->description = "Machinery";
        
        $AssetTypeTable->add($assetType);
        var_dump($AssetTypeTable->fetchAll());
        }

}

