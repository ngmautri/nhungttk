<?php
namespace ApplicationTest\MessageStore;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\Doctrine\MessageStoreRepository;
use PHPUnit_Framework_TestCase;

class MessageRepositoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new MessageStoreRepository($em);
        $results = $rep->getMessages(301, "1c014799-c3c0-44c6-aef5-a70e685f957c");
        var_dump($results);
    }
}