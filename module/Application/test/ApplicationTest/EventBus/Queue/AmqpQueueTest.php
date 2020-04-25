<?php
namespace ApplicationTest\EventBus\Queue;

use ApplicationTest\EventBus\DummyEvent;
use Application\Domain\EventBus\Queue\AmqpQueue;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit_Framework_TestCase;

class AmqpQueueTest extends PHPUnit_Framework_TestCase
{

    /** @var AMQPStreamConnection */
    protected $producerConnection;

    /** @var AMQPStreamConnection */
    protected $consumerConnection;

    /** @var AmqpQueue */
    protected $consumer;

    /** @var AmqpQueue */
    protected $producer;

    private $host = 'zebra.rmq.cloudamqp.com';

    private $post = 5672;

    private $user = 'roblgcxy';

    private $pass = 'dfnRxpAByZYpmxCTA-g_r_u1zqkXoViP';

    private $vhost = 'roblgcxy';

    public function setUp()
    {
        $this->consumerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);
        $this->producerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);

        $this->producer = new AmqpQueue($this->producerConnection, 'testAdapterQueueNMT');
        $this->consumer = new AmqpQueue($this->consumerConnection, 'testAdapterQueueNMT');
    }

    public function testAdapterQueue()
    {
        $event = new DummyEvent();
        $this->producer->push($event);
        var_dump($this->consumer->pop());

        // $this->assertEquals($event, $this->consumer->pop());
    }

    public function testName()
    {
        $this->assertEquals('testAdapterQueueNMT', $this->consumer->name());
    }
}