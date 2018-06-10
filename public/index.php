<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
 
 
 // Report all errors except E_NOTICE
//error_reporting(E_ALL & ~E_NOTICE);

// Report all PHP errors
//error_reporting(-1);

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
//error_reporting(E_ERROR |  E_PARSE | E_NOTICE);

error_reporting(E_ERROR |  E_PARSE);

//error_reporting(E_ALL);

chdir ( dirname ( __DIR__ ) );

define ( 'ROOT', realpath ( dirname ( dirname ( __FILE__ ) ) ) );
define('TIMESTAMP_START', microtime(true));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init ( require 'config/application.config.php' )->run ();
