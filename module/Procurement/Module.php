<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procurement;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;

use Procurement\Model\PurchaseRequest;
use Procurement\Model\PurchaseRequestTable;

use Procurement\Model\PurchaseRequestItem;
use Procurement\Model\PurchaseRequestItemTable;

use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;

class Module {
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
						
						// PurchaseRequestTable
						'Procurement\Model\PurchaseRequestTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PurchaseRequestTableGateway' );
						$table = new PurchaseRequestTable( $tableGateway );
						return $table;
						},
	
						'PurchaseRequestTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PurchaseRequest() );
						return new TableGateway ( 'mla_purchase_requests', $dbAdapter, null, $resultSetPrototype );
						},
						
						// PurchaseRequestItemTable
						'Procurement\Model\PurchaseRequestItemTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PurchaseRequestItemTableGateway' );
						$table = new PurchaseRequestItemTable($tableGateway );
						return $table;
						},
						
						'PurchaseRequestItemTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PurchaseRequestItem() );
						return new TableGateway ( 'mla_purchase_request_items', $dbAdapter, null, $resultSetPrototype );
						},
						

						// PurchaseRequestItemPicTable
						'Procurement\Model\PurchaseRequestItemPicTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PurchaseRequestItemPicTableGateway' );
						$table = new PurchaseRequestItemPicTable($tableGateway );
						return $table;
						},
						
						'PurchaseRequestItemPicTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PurchaseRequestItemPic());
						return new TableGateway ( 'mla_purchase_request_item_pics', $dbAdapter, null, $resultSetPrototype );
						},
	
					)
				);
	}
}
