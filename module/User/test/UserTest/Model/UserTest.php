<?php
namespace UserTest\Model;

use PHPUnit_Framework_TestCase;
use User\Model\UserTable;
use Zend\Db\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use UserTest\Bootstrap;

class UserTest extends PHPUnit_Framework_TestCase{

    	public function testUserTable (){

        $resultSet = new ResultSet();
        $userTable  =  Bootstrap::getServiceManager()->get('User\Model\UserTable');
        var_dump($userTable->getUserByID(1));
	}

}

