<?php
namespace ApplicationTest\EventBus\Worker;

use ApplicationTest\Bootstrap;
use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\Middleware\ProducerEventBusMiddleware;
use Application\Domain\EventBus\Queue\AmqpQueue;
use Application\Domain\EventBus\Queue\DoctrineQueue;
use Application\Domain\EventBus\Queue\Worker\SimpleProcurerWorker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit_Framework_TestCase;

class ProducerWorker1Test extends PHPUnit_Framework_TestCase
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

    protected $inputQueue;

    protected $producerEventBus;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        require ($root . '/Bootstrap.php');

        $this->consumerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);
        $this->producerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);

        $this->producer = new AmqpQueue($this->producerConnection, 'testQueueName');

        $this->producerEventBus = new EventBus([
            new ProducerEventBusMiddleware($this->producer)
        ]);
    }

    public function testAdapterQueue()
    {
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $this->inputQueue = new DoctrineQueue($doctrineEM, "testQueueName");

        $events = $this->inputQueue->pop();
        $producerWorker = new SimpleProcurerWorker();
        $producerWorker->send($events, $this->producerEventBus);
    }
}