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

use Procurement\Model\Delivery;
use Procurement\Model\DeliveryTable;

use Procurement\Model\DeliveryItem;
use Procurement\Model\DeliveryItemTable;

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
						
						// DeliveryTable
						'Procurement\Model\DeliveryTable' => function ($sm) {
						$tableGateway = $sm->get ( 'DeliveryTableGateway' );
						$table = new DeliveryTable($tableGateway );
						return $table;
						},
						
						'DeliveryTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new Delivery());
						return new TableGateway ( 'mla_delivery', $dbAdapter, null, $resultSetPrototype );
						},
						
						// DeliveryItemTable
						'Procurement\Model\DeliveryItemTable' => function ($sm) {
						$tableGateway = $sm->get ( 'DeliveryItemTableGateway' );
						$table = new DeliveryItemTable($tableGateway );
						return $table;
						},
						
						'DeliveryItemTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new DeliveryItem());
						return new TableGateway ( 'mla_delivery_items', $dbAdapter, null, $resultSetPrototype );
						},
	
					)
				);
	}
}
