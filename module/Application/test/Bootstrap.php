<?php
namespace ApplicationTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Db\Adapter\Adapter;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use RuntimeException;
use Zend\EventManager\EventManager;
error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{

    protected static $serviceManager;

    public static function init()
    {
        $zf2ModulePaths = array(
            dirname(dirname(__DIR__))
        );
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::initAutoloader();

        $rootPath = dirname(static::findParentPath('module'));

        // use ModuleManager to load this module and it's dependencies

        // application config
        $config = array(

            'modules' => array(
                'DoctrineModule',
                'DoctrineORMModule',
                'Application',
                'Inventory',    
                'User',
            ),

            'module_listener_options' => array(
                'module_paths' => $zf2ModulePaths,

                // An array of paths from which to glob configuration files after
                // modules are loaded. These effectively overide configuration
                // provided by modules themselves. Paths may use GLOB_BRACE notation.
                'config_glob_paths' => array(
                    // '/config/autoload/{,*.}{global,local}.php'
                    $rootPath . '/config/autoload/{,*.}{global,local}.php'
                )
            )
        );

        // ServiceManager Config
        $smConfig = array();
        //
      
        $serviceManager = new ServiceManager(new ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $config);
        
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        // echo $rootPath;

        chdir($rootPath);
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');
        $modulePath = static::findParentPath('module');

        $zf2Path = getenv('ZF2_PATH');
        if (! $zf2Path) {
            if (defined('ZF2_PATH')) {
                $zf2Path = ZF2_PATH;
            } elseif (is_dir($vendorPath . '/ZF2/library')) {
                $zf2Path = $vendorPath . '/ZF2/library';
            } elseif (is_dir($vendorPath . '/zendframework/zendframework/library')) {
                $zf2Path = $vendorPath . '/zendframework/zendframework/library';
            }
        }

        if (! $zf2Path) {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or' . ' define a ZF2_PATH environment variable.');
        }

        if (file_exists($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
        }

        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                    'MLA' => $vendorPath . DIRECTORY_SEPARATOR . 'MLA' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'MLA',
                    'ZendSearch' => $vendorPath . DIRECTORY_SEPARATOR . 'ZendSearch' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'ZendSearch'
                )
            )
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (! is_dir($dir . DIRECTORY_SEPARATOR . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir)
                return false;
            $previousDir = $dir;
        }
        return $dir . DIRECTORY_SEPARATOR . $path;
    }
}

define('ROOT', realpath(dirname(dirname(__FILE__))));

Bootstrap::init();
Bootstrap::chroot();


