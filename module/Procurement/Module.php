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

use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;

use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;

use Procurement\Model\Delivery;
use Procurement\Model\DeliveryTable;

use Procurement\Model\DeliveryItem;
use Procurement\Model\DeliveryItemTable;

use Procurement\Model\Vendor;
use Procurement\Model\VendorTable;

use Procurement\Model\PRWorkFlow;
use Procurement\Model\PRWorkFlowTable;

use Procurement\Model\PRItemWorkFlow;
use Procurement\Model\PRItemWorkFlowTable;

use Procurement\Model\DeliveryWorkFlow;
use Procurement\Model\DeliveryWorkFlowTable;

use Procurement\Model\DeliveryItemWorkFlow;
use Procurement\Model\DeliveryItemWorkFlowTable;

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
						
						// PurchaseRequestCartItemTable
						'Procurement\Model\PurchaseRequestCartItemTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PurchaseRequestCartItemTableGateway' );
						$table = new PurchaseRequestCartItemTable($tableGateway );
						return $table;
						},
						
						'PurchaseRequestCartItemTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PurchaseRequestCartItem() );
						return new TableGateway ( 'mla_purchase_cart', $dbAdapter, null, $resultSetPrototype );
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
						
						// VendorTable
						'Procurement\Model\VendorTable' => function ($sm) {
						$tableGateway = $sm->get ( 'VendorTableGateway' );
						$table = new VendorTable($tableGateway);
						return $table;
						},
						
						'VendorTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new Vendor());
						return new TableGateway ( 'mla_vendors', $dbAdapter, null, $resultSetPrototype );
						},
						// PRWorkFlowTable
						'Procurement\Model\PRWorkFlowTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PRWorkFlowTableGateway' );
						$table = new PRWorkFlowTable($tableGateway);
						return $table;
						},
						
						'PRWorkFlowTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PRWorkFlow());
						return new TableGateway ( 'mla_purchase_requests_workflows', $dbAdapter, null, $resultSetPrototype );
						},
						
						// PRItemWorkFlowTable
						'Procurement\Model\PRItemWorkFlowTable' => function ($sm) {
						$tableGateway = $sm->get ( 'PRItemWorkFlowTableGateway' );
						$table = new PRItemWorkFlowTable($tableGateway);
						return $table;
						},
						
						'PRItemWorkFlowTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new PRItemWorkFlow());
						return new TableGateway ( 'mla_purchase_requests_items_workflows', $dbAdapter, null, $resultSetPrototype );
						},
						

						// DeliveryWorkFlowTable
						'Procurement\Model\DeliveryWorkFlowTable' => function ($sm) {
						$tableGateway = $sm->get ( 'DeliveryWorkFlowTableGateway' );
						$table = new DeliveryWorkFlowTable($tableGateway);
						return $table;
						},
						
						'DeliveryWorkFlowTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new DeliveryWorkFlow());
						return new TableGateway ( 'mla_delivery_workflows', $dbAdapter, null, $resultSetPrototype );
						},
						
						// DeliveryItemWorkFlowTable
						'Procurement\Model\DeliveryItemWorkFlowTable' => function ($sm) {
						$tableGateway = $sm->get ( 'DeliveryItemWorkFlowTableGateway' );
						$table = new DeliveryItemWorkFlowTable($tableGateway);
						return $table;
						},
						
						'DeliveryItemWorkFlowTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new DeliveryItemWorkFlow());
						return new TableGateway ( 'mla_delivery_items_workflows', $dbAdapter, null, $resultSetPrototype );
						},
						
						
					)
				);
	}
}
