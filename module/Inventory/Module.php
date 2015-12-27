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
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Inventory\Model\AssetType;
use Inventory\Model\AssetTypeTable;


class Module
{

	/*
	 * The init() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */

	/*
	public function init(ModuleManager $moduleManager)
	{
		// Remember to keep the init() method as lightweight as possible
		$events = $moduleManager->getEventManager();
		$events->attach('loadModules.post', array($this, 'modulesLoaded'));
	}
	*/

	/*
	 * The onBootstrap() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */
	public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {
            sprintf('executed on dispatch process');
         });
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    // Add this method:
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Inventory\Model\AssetTypeTable' =>  function($sm) {
    						$tableGateway = $sm->get('AssetTypeTableGateway');
    						$table = new AssetTypeTable($tableGateway);
    						return $table;
    					},

    					'AssetTypeTableGateway' => function ($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new AssetType());
    						return new TableGateway('mla_asset_types', $dbAdapter, null, $resultSetPrototype);
    					},
    			),
    	);
    }

}
