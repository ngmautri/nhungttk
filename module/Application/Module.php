<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Application\Model\Department;
use Application\Model\DepartmentTable;

use Application\Model\DepartmentMember;
use Application\Model\DepartmentMemberTable;

use Application\Model\UOM;
use Application\Model\UOMTable;

use Application\Model\Currency;
use Application\Model\CurrencyableTable;
use Application\Model\CurrencyTable;

class Module {
	

	public function onBootstrap(MvcEvent $e) {
		
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
		
		/*
		$eventManager = $e->getApplication ()->getEventManager ();
		
		$eventManager->attach ( MvcEvent::EVENT_DISPATCH, function ($e) {
			sprintf ( 'executed on dispatch process' );
		} );
		*/
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
	public function getServiceConfig() {
		return array (
				'factories' => array (
						
						// Department Table
						'Application\Model\DepartmentTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DepartmentTableGateway' );
							$table = new DepartmentTable( $tableGateway );
							return $table;
						},
						
						'DepartmentTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Department() );
							return new TableGateway ( 'mla_departments', $dbAdapter, null, $resultSetPrototype );
						} ,
						
						// Department MemberTable
						'Application\Model\DepartmentMemberTable' => function ($sm) {
						$tableGateway = $sm->get ( 'DepartmentMemberTableGateway' );
						$table = new DepartmentMemberTable( $tableGateway );
						return $table;
						},
						
						'DepartmentMemberTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new DepartmentMember() );
						return new TableGateway ( 'mla_departments_members', $dbAdapter, null, $resultSetPrototype );
						} ,
						
						// CurrencyTable
						'Application\Model\CurrencyTable' => function ($sm) {
						$tableGateway = $sm->get ( 'CurrencyTableGateway' );
						$table = new CurrencyTable( $tableGateway );
						return $table;
						},
						
						'CurrencyTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new Currency() );
						return new TableGateway ( 'nmt_application_currency', $dbAdapter, null, $resultSetPrototype );
						} ,
						
						// UOM Table
						'Application\Model\UOMTable' => function ($sm) {
						$tableGateway = $sm->get ( 'UOMTableGateway' );
						$table = new UOMTable( $tableGateway );
						return $table;
						},
						
						'UOMTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new UOM() );
						return new TableGateway ( 'nmt_application_uom', $dbAdapter, null, $resultSetPrototype );
						} ,
						
						
						
				)
				,
				
				'invokables' => array ()

				 
		);
	}
}
