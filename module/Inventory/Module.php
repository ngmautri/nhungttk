<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

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

use Inventory\Model\SparepartCategory;
use Inventory\Model\SparepartCategoryTable;

use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;

use Inventory\Model\AssetCounting;
use Inventory\Model\AssetCountingTable;

use Inventory\Model\AssetCountingItem;
use Inventory\Model\AssetCountingItemTable;

use Inventory\Model\Article;
use Inventory\Model\ArticleTable;

use Inventory\Model\ArticleCategory;
use Inventory\Model\ArticleCategoryTable;

use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;

use Inventory\Model\ArticlePicture;
use Inventory\Model\ArticlePictureTable;

use Inventory\Model\ArticleMovement;
use Inventory\Model\ArticleMovementTable;

use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;

use Inventory\Model\SparepartLastDN;
use Inventory\Model\SparepartLastDNTable;



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
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
	
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
							$table = new AssetCategoryTable ( $tableGateway );
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
							$table = new AssetGroupTable ( $tableGateway );
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
							$table = new MLAAssetTable ( $tableGateway );
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
							$table = new AssetPictureTable ( $tableGateway );
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
							$table = new MLASparepartTable ( $tableGateway );
							return $table;
						},
						
						'MLASparepartGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new MLASparepart () );
							return new TableGateway ( 'mla_spareparts', $dbAdapter, null, $resultSetPrototype );
						},
						
						// SparepartPicture
						'Inventory\Model\SparepartPictureTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SparepartPictureGateway' );
							$table = new SparepartPictureTable ( $tableGateway );
							return $table;
						},
						
						'SparepartPictureGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new SparepartPicture () );
							return new TableGateway ( 'mla_sparepart_pics', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Spare part movement
						'Inventory\Model\SparepartMovementsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'SparepartMovementsGateway' );
							$table = new SparepartMovementsTable ( $tableGateway );
							return $table;
						},
						
						'SparepartMovementsGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new SparepartMovement () );
							return new TableGateway ( 'mla_sparepart_movements', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Spare part Category
						'Inventory\Model\SparepartCategoryTable' => function ($sm) {
						$tableGateway = $sm->get ( 'SparepartCategoryGateway' );
						$table = new SparepartCategoryTable( $tableGateway );
						return $table;
						},
						
						'SparepartCategoryGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new SparepartCategory() );
						return new TableGateway ( 'mla_sparepart_cats', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Spare part Category Member
						'Inventory\Model\SparepartCategoryMemberTable' => function ($sm) {
						$tableGateway = $sm->get ( 'SparepartCategoryMemberGateway' );
						$table = new SparepartCategoryMemberTable( $tableGateway );
						return $table;
						},
						
						'SparepartCategoryMemberGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new SparepartCategoryMember() );
						return new TableGateway ( 'mla_sparepart_cats_members', $dbAdapter, null, $resultSetPrototype );
						},
						
						// AssetCounting Table
						'Inventory\Model\AssetCountingTable' => function ($sm) {
						$tableGateway = $sm->get ( 'AssetCountingTableGateway' );
						$table = new AssetCountingTable($tableGateway );
						return $table;
						},
						
						'AssetCountingTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new AssetCounting() );
						return new TableGateway ( 'mla_asset_counting', $dbAdapter, null, $resultSetPrototype );
						},
						
						// AssetCountingItem Table
						'Inventory\Model\AssetCountingItemTable' => function ($sm) {
						$tableGateway = $sm->get ( 'AssetCountingItemTableGateway' );
						$table = new AssetCountingItemTable($tableGateway );
						return $table;
						},
						
						'AssetCountingItemTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new AssetCountingItem() );
						return new TableGateway ( 'mla_asset_counting_items', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Article Table
						'Inventory\Model\ArticleTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticleTableGateway' );
						$table = new ArticleTable($tableGateway );
						return $table;
						},
						
						'ArticleTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new Article() );
						return new TableGateway ( 'mla_articles', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Article Picture Table
						'Inventory\Model\ArticlePictureTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticlePictureTableGateway' );
						$table = new ArticlePictureTable($tableGateway );
						return $table;
						},
						
						'ArticlePictureTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new ArticlePicture() );
						return new TableGateway ( 'mla_articles_pics', $dbAdapter, null, $resultSetPrototype );
						},
						

						// Article Category Table
						'Inventory\Model\ArticleCategoryTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticleCategoryTableGateway' );
						$table = new ArticleCategoryTable($tableGateway );
						return $table;
						},
						
						'ArticleCategoryTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new ArticleCategory() );
						return new TableGateway ( 'mla_articles_categories', $dbAdapter, null, $resultSetPrototype );
						},
						

						// Article Category Member Table
						'Inventory\Model\ArticleCategoryMemberTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticleCategoryMemberTableGateway' );
						$table = new ArticleCategoryMemberTable($tableGateway);
						return $table;
						},
						
						'ArticleCategoryMemberTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new ArticleCategoryMember() );
						return new TableGateway ( 'mla_articles_categories_members', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Article Movement Table
						'Inventory\Model\ArticleMovementTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticleMovementTableGateway' );
						$table = new ArticleMovementTable($tableGateway);
						return $table;
						},
						
						'ArticleMovementTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new ArticleMovement() );
						return new TableGateway ( 'mla_articles_movements', $dbAdapter, null, $resultSetPrototype );
						},
						
						// Article Last DN Table
						'Inventory\Model\ArticleLastDNTable' => function ($sm) {
						$tableGateway = $sm->get ( 'ArticleLastDNTableGateway' );
						$table = new ArticleLastDNTable($tableGateway);
						return $table;
						},
						
						'ArticleLastDNTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new ArticleLastDN() );
						return new TableGateway ( 'mla_articles_last_dn', $dbAdapter, null, $resultSetPrototype );
						},

						// SP Last DN Table
						'Inventory\Model\SparepartLastDNTable' => function ($sm) {
						$tableGateway = $sm->get ( 'SparepartLastDNTableGateway' );
						$table = new SparepartLastDNTable($tableGateway);
						return $table;
						},
						
						'SparepartLastDNTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new SparepartLastDN() );
						return new TableGateway ( 'mla_spareparts_last_dn', $dbAdapter, null, $resultSetPrototype );
						},
						
						
						'Inventory\Services\AssetSearchService'  => 'Inventory\Services\AssetSearchServiceFactory',
						'Inventory\Services\SparePartsSearchService'  => 'Inventory\Services\SparePartsSearchServiceFactory',
						'Inventory\Services\ArticleSearchService'  => 'Inventory\Services\ArticleSearchServiceFactory',
						'Inventory\Listener\PictureUploadListener' => 'Inventory\Listener\PictureUploadListenerFactory',
				)
				,
				
				'invokables' => array (
						'Inventory\Services\AssetService' => 'Inventory\Services\AssetService',
						'Inventory\Services\SparepartService' => 'Inventory\Services\SparepartService',
						'Inventory\Services\ArticleService' => 'Inventory\Services\ArticleService',
						
				) 
		);
	}
}
