<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory;

/*
 *
 */
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;

use Inventory\Model\AssetCategory;
use Inventory\Model\AssetCategoryTable;

use Inventory\Model\AssetGroup;
use Inventory\Model\AssetGroupTable;

use Inventory\Model\MLAAsset;
use Inventory\Model\MLAAssetTable;

use Inventory\Model\AssetPicture;
use Inventory\Model\AssetPictureTable;

use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;

use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;

use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;


class Module {
	
	/*
	 * The init() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */
	
	/*
	 * public function init(ModuleManager $moduleManager)
	 * {
	 * // Remember to keep the init() method as lightweight as possible
	 * $events = $moduleManager->getEventManager();
	 * $events->attach('loadModules.post', array($this, 'modulesLoaded'));
	 * }
	 */
	
	/*
	 * The onBootstrap() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		
		$eventManager->attach ( MvcEvent::EVENT_DISPATCH, function ($e) {
			sprintf ( 'executed on dispatch process' );
		} );
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ 
						) 
				) 
		);
	}
	
	// Add this method:
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'Inventory\Model\AssetCategoryTable' => function ($sm) {
							$tableGateway = $sm->get ( 'AssetCategoryTableGateway' );
							$table = new AssetCategoryTable( $tableGateway );
							return $table;
						},
						
						'AssetCategoryTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new AssetCategory () );
							return new TableGateway ( 'mla_asset_categories', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Asset Group
						'Inventory\Model\AssetGroupTable' => function ($sm) {
						$tableGateway = $sm->get ( 'AssetGroupTableGateway' );
						$table = new AssetGroupTable( $tableGateway );
						return $table;
						},
						
						'AssetGroupTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new AssetGroup () );
						return new TableGateway ( 'mla_asset_group', $dbAdapter, null, $resultSetPrototype );
						},
						
						// MLA Asset
						'Inventory\Model\MLAAssetTable' => function ($sm) {
						$tableGateway = $sm->get ( 'MLAAssetTableGateway' );
						$table = new MLAAssetTable( $tableGateway );
						return $table;
						},
						
						'MLAAssetTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new MLAAsset () );
						return new TableGateway ( 'mla_asset', $dbAdapter, null, $resultSetPrototype );
						},
						
						// AssetPicture
						'Inventory\Model\AssetPictureTable' => function ($sm) {
						$tableGateway = $sm->get ( 'AssetPictureGateway' );
						$table = new AssetPictureTable( $tableGateway );
						return $table;
						},
						
						'AssetPictureGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new AssetPicture () );
						return new TableGateway ( 'mla_asset_pics', $dbAdapter, null, $resultSetPrototype );
						},
						
						// MLA Sparepart
						'Inventory\Model\MLASparepartTable' => function ($sm) {
						$tableGateway = $sm->get ( 'MLASparepartGateway' );
						$table = new MLASparepartTable( $tableGateway );
						return $table;
						},
						
						'MLASparepartGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new MLASparepart() );
						return new TableGateway ( 'mla_spareparts', $dbAdapter, null, $resultSetPrototype );
						},
						
						// SparepartPicture
						'Inventory\Model\SparepartPictureTable' => function ($sm) {
						$tableGateway = $sm->get ( 'SparepartPictureGateway' );
						$table = new SparepartPictureTable( $tableGateway );
						return $table;
						},
						
						'SparepartPictureGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new SparepartPicture() );
						return new TableGateway ( 'mla_sparepart_pics', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Spare part movement
						'Inventory\Model\SparepartMovementsTable' => function ($sm) {
						$tableGateway = $sm->get ( 'SparepartMovementsGateway' );
						$table = new SparepartMovementsTable( $tableGateway );
						return $table;
						},
						
						'SparepartMovementsGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new SparepartMovement() );
						return new TableGateway ( 'mla_sparepart_movements', $dbAdapter, null, $resultSetPrototype );
						},
						
				),
				
				
				
				
				'invokables' => array (
						'Inventory\Services\AssetService' => 'Inventory\Services\AssetService',
						'Inventory\Services\SparepartService' => 'Inventory\Services\SparepartService'
				)
		);
	}
}
