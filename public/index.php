<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir ( dirname ( __DIR__ ) );

define ( 'ROOT', realpath ( dirname ( dirname ( __FILE__ ) ) ) );

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init ( require 'config/application.config.php' )->run ();
