<?php
namespace ApplicationTest\EventBus\Queue;

use ApplicationTest\Bootstrap;
use Application\Domain\EventBus\Queue\DoctrineQueue;
use PHPUnit_Framework_TestCase;

class AmqpQueueTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testAdapterQueue()
    {
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $queue = new DoctrineQueue($doctrineEM, "testQueueName");

        /*
         * for ($i = 1; $i <= 10; ++ $i) {
         * $event = new DummyEvent();
         * $queue->push($event);
         * }
         */

        var_dump($queue->pop());
    }
}