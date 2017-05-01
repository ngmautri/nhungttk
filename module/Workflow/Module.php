<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Workflow;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Workflow\Model\NmtWfWorkflow;
use Workflow\Model\NmtWfWorkflowTable;

use Workflow\Model\NmtWfNode;
use Workflow\Model\NmtWfNodeTable;

use Workflow\Model\NmtWfCase;
use Workflow\Model\NmtWfCaseTable;

use Workflow\Model\NmtWfToken;
use Workflow\Model\NmtWfTokenTable;

use Workflow\Model\NmtWfTransition;
use Workflow\Model\NmtWfTransitionTable;

use Workflow\Model\NmtWfPlace;
use Workflow\Model\NmtWfPlaceTable;

use Workflow\Model\NmtWfWorkitem;
use Workflow\Model\NmtWfWorkitemTable;


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
	
	/**
	 * 
	 * @return unknown
	 */
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
						
						//nmt_wf_workflow
						'Workflow\Model\NmtWfWorkflowTable' => function ($sm) {
							$tableGateway = $sm->get ( 'NmtWfWorkflowTableGateway' );
							$table = new NmtWfWorkflowTable( $tableGateway );
							return $table;
						},
						
						'NmtWfWorkflowTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new NmtWfWorkflow() );
							return new TableGateway ( 'nmt_wf_workflow', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_node
						'Workflow\Model\NmtWfNodeTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfNodeTableGateway' );
						$table = new NmtWfNodeTable($tableGateway );
						return $table;
						},
						
						'NmtWfNodeTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfNode() );
						return new TableGateway ( 'nmt_wf_node', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_place
						'Workflow\Model\NmtWfPlaceTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfPlaceTableGateway' );
						$table = new NmtWfPlaceTable($tableGateway );
						return $table;
						},
						
						'NmtWfPlaceTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfPlace() );
						return new TableGateway ( 'nmt_wf_place', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_transition
						'Workflow\Model\NmtWfTransitionTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfTransitionTableGateway' );
						$table = new NmtWfTransitionTable($tableGateway );
						return $table;
						},
						
						'NmtWfTransitionTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfTransition() );
						return new TableGateway ( 'nmt_wf_transition', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_case						 
						'Workflow\Model\NmtWfCaseTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfCaseTableGateway' );
						$table = new NmtWfCaseTable($tableGateway );
						return $table;
						},
						
						'NmtWfCaseTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfCase() );
						return new TableGateway ( 'nmt_wf_case', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_token
						'Workflow\Model\NmtWfTokenTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfTokenTableGateway' );
						$table = new NmtWfTokenTable($tableGateway );
						return $table;
						},
						
						'NmtWfTokenTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfToken() );
						return new TableGateway ( 'nmt_wf_token', $dbAdapter, null, $resultSetPrototype );
						},
						
						//nmt_wf_workitem
						'Workflow\Model\NmtWfWorkitemTable' => function ($sm) {
						$tableGateway = $sm->get ( 'NmtWfWorkitemTableGateway' );
						$table = new NmtWfWorkitemTable($tableGateway );
						return $table;
						},
						
						'NmtWfWorkitemTableGateway' => function ($sm) {
						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						$resultSetPrototype = new ResultSet ();
						$resultSetPrototype->setArrayObjectPrototype ( new NmtWfWorkitem() );
						return new TableGateway ( 'nmt_wf_workitem', $dbAdapter, null, $resultSetPrototype );
						},
					) 
		);
	}
}
