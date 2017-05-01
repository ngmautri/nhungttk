<?php

namespace WorkflowTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Db\Adapter\Adapter;
use RuntimeException;
use User\Module;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;


error_reporting ( E_ALL | E_STRICT );
chdir ( __DIR__ );

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap {
	protected static $serviceManager;
	public static function init() {
		$zf2ModulePaths = array (
				dirname ( dirname ( __DIR__ ) ) 
		);
		if (($path = static::findParentPath ( 'vendor' ))) {
			$zf2ModulePaths [] = $path;
		}
		if (($path = static::findParentPath ( 'module' )) !== $zf2ModulePaths [0]) {
			$zf2ModulePaths [] = $path;
		}
		
		static::initAutoloader ();
		
		// use ModuleManager to load this module and it's dependencies
		
		// application config
		$config = array (
				'module_listener_options' => array (
						'module_paths' => $zf2ModulePaths 
				),
				'modules' => array (
						'User',
						'Workflow',
							
				) 
		)
		;
		
		// ServiceManager Config
		$smConfig = array (
				'factories' => array (
						'Zend\Db\Adapter\Adapter' => function ($sm) {
							return $adapter = new Adapter ( array (
									'driver' => 'Pdo_Mysql',
									'hostname' => 'localhost',
									'database' => 'mla',
									'username' => 'root',
									'password' => '' 
							) );
						},
						
						// Email Service
						'SmtpTransportService' => function ($sm) {
							
						$transport = new SmtpTransport ();
						$options = new SmtpOptions ( array (
								'name' => 'Web.de',
								'host' => 'smtp.web.de',
								'port' => '587',
								'connection_class' => 'login',
								'connection_config' => array (
										'username' => 'mib-team@web.de',
										'password' => 'mib2009',
										'ssl' => 'tls'
								)
						) );
							
						$transport->setOptions ( $options );
						return $transport;
						},
						
						
						// Email Service
						'mla-web@outlook.com' => function ($sm) {
							
						$transport = new SmtpTransport ();
						$options = new SmtpOptions ( array (
								'name' => 'Outlook.com',
								'host' => 'smtp-mail.outlook.com',
								'port' => '587',
								'connection_class' => 'login',
								'connection_config' => array (
										'username' => 'mla-app@outlook.com',
										'password' => 'MLA#2016',
										'ssl' => 'tls'
								)
						) );
							
							
						$transport->setOptions ( $options );
						return $transport;
						}
						
				)
				
		);
		
		$serviceManager = new ServiceManager ( new ServiceManagerConfig ( $smConfig ) );
		$serviceManager->setService ( 'ApplicationConfig', $config );
		
		// load modules -- which will provide services, configuration, and more
		$serviceManager->get('ModuleManager')->loadModules();
		
		static::$serviceManager = $serviceManager;
	}
	public static function chroot() {
		$rootPath = dirname ( static::findParentPath ( 'module' ) );
		chdir ( $rootPath );
	}
	public static function getServiceManager() {
		return static::$serviceManager;
	}
	protected static function initAutoloader() {
		$vendorPath = static::findParentPath ( 'vendor' );
		$modulePath = static::findParentPath ( 'module' );
		
		$zf2Path = getenv ( 'ZF2_PATH' );
		if (! $zf2Path) {
			if (defined ( 'ZF2_PATH' )) {
				$zf2Path = ZF2_PATH;
			} elseif (is_dir ( $vendorPath . '/ZF2/library' )) {
				$zf2Path = $vendorPath . '/ZF2/library';
			} elseif (is_dir ( $vendorPath . '/zendframework/zendframework/library' )) {
				$zf2Path = $vendorPath . '/zendframework/zendframework/library';
			}
		}
		
		if (! $zf2Path) {
			throw new RuntimeException ( 'Unable to load ZF2. Run `php composer.phar install` or' . ' define a ZF2_PATH environment variable.' );
		}
		
		if (file_exists ( $vendorPath . '/autoload.php' )) {
			include $vendorPath . '/autoload.php';
		}
		
		include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
		AutoloaderFactory::factory ( array (
				'Zend\Loader\StandardAutoloader' => array (
						'autoregister_zf' => true,
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__ 
						) 
				) 
		) );
	}
	protected static function findParentPath($path) {
		$dir = __DIR__;
		$previousDir = '.';
		while ( ! is_dir ( $dir . '/' . $path ) ) {
			$dir = dirname ( $dir );
			if ($previousDir === $dir)
				return false;
			$previousDir = $dir;
		}
		return $dir . '/' . $path;
	}
}

Bootstrap::init ();
Bootstrap::chroot ();


