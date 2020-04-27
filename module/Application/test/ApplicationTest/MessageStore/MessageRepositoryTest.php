<?php
namespace ApplicationTest\MessageStore;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\Doctrine\MessageStoreRepository;
use PHPUnit_Framework_TestCase;

class MessageRepositoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new MessageStoreRepository($em);
        $results = $rep->getMessages(342, "73472fa5-80c5-4f38-8f74-ceb90e867600");
        var_dump($results);
    }
}