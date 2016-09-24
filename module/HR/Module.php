<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;


use HR\Model\Vendor;
use HR\Model\VendorTable;

use HR\Model\Employee;
use HR\Model\EmployeeTable;

use HR\Model\EmployeePicture;
use HR\Model\EmployeePictureTable;

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

						// VendorTable
						'HR\Model\VendorTable' => function ($sm) {
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
						
						// EmployeeTable
						'HR\Model\EmployeeTable' => function ($sm) {
						$tableGateway = $sm->get ( 'EmployeeTableGateway' );
						$table = new EmployeeTable($tableGateway);
						return $table;
						},
						
						'EmployeeTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new Employee());
						return new TableGateway ('hr_employee', $dbAdapter, null, $resultSetPrototype );
						},
						
						// EmployeeTable
						'HR\Model\EmployeePictureTable' => function ($sm) {
						$tableGateway = $sm->get ( 'EmployeePictureTableGateway' );
						$table = new EmployeePictureTable($tableGateway);
						return $table;
						},
						
						'EmployeePictureTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new EmployeePicture());
						return new TableGateway ('hr_employee_picture', $dbAdapter, null, $resultSetPrototype );
						},
						
						'HR\Services\EmployeeSearchService' => 'HR\Services\EmployeeSearchServiceFactory',
						
						
					),
					'invokables' => array (
							'HR\Services\EmployeeService' => 'HR\Services\EmployeeService',
					)
				);
	}
}
