<?php
namespace UserTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;

use User\Model\UserTable;
use UserTest\Bootstrap;

class UserTest extends PHPUnit_Framework_TestCase{

    	public function testUserTable (){

        $resultSet = new ResultSet();
        $userTable  =  Bootstrap::getServiceManager()->get('User\Model\UserTable');
        var_dump($userTable->getUserByID(1));
		}

	public function testDI(){

		$callback = function($event){
			echo "An event has happend \n";
			var_dump($event->getName());
			var_dump($event->getParams());
		};

		$eventManager = new EventManager();
		$eventManager->attach('eventName',$callback);
		echo "\nRaise an event\n";
		$eventManager->trigger('eventName', null, array('one'=>1,'two'=>2));

	}
}

