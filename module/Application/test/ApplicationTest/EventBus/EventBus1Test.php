<?php
namespace ApplicationTest\EventBus\Middleware;

use ApplicationTest\Bootstrap;
use ApplicationTest\EventBus\DummyEvent;
use ApplicationTest\EventBus\DummyEvent2Handler;
use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Domain\EventBus\Handler\Resolver\SimpleArrayResolver;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use Application\Domain\EventBus\Middleware\LoggerEventBusMiddleware;
use Application\Domain\EventBus\Middleware\ProducerEventBusMiddleware;
use Application\Domain\EventBus\Queue\AmqpQueue;
use Application\Domain\EventBus\Queue\DoctrineQueue;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit_Framework_TestCase;

class EventBus1Test extends PHPUnit_Framework_TestCase
{

    protected $handlers;

    protected $mapper;

    protected $resolver;

    /** @var AMQPStreamConnection */
    protected $producerConnection;

    /** @var AMQPStreamConnection */
    protected $consumerConnection;

    /** @var AmqpQueue */
    protected $consumer;

    /** @var AmqpQueue */
    protected $producer;

    protected $queueName = 'testQueueName';

    protected $host = 'zebra.rmq.cloudamqp.com';

    protected $post = 5672;

    protected $user = 'roblgcxy';

    protected $pass = 'dfnRxpAByZYpmxCTA-g_r_u1zqkXoViP';

    protected $vhost = 'roblgcxy';

    protected $doctrineQueue;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        require ($root . '/Bootstrap.php');

        $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },

            DummyEvent2Handler::class => function () {
                return new DummyEvent2Handler();
            }
        ];

        $this->resolver = new SimpleArrayResolver($handlers);

        $this->mapper = new FullNameHandlerMapper([
            DummyEventHandler::class,
            DummyEvent2Handler::class
        ]);

        $this->resolver = new SimpleArrayResolver($handlers);

        $this->producerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);

        $this->producer = new AmqpQueue($this->producerConnection, $this->queueName);

        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $this->doctrineQueue = new DoctrineQueue($doctrineEM, $this->queueName);
    }

    public function testItCanStackMiddleware()
    {
        // $logger = new InMemoryLogger();
        $logger = new Logger("EventBus");

        $path = __DIR__ . '/log/';
        if (false === file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $handler = new StreamHandler($path . '/test.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $middleware = [
            new LoggerEventBusMiddleware($logger),
            new ProducerEventBusMiddleware($this->producer)
            // new EventBusMiddleware($this->mapper, $this->resolver)
        ];

        $middleware1 = [
            new LoggerEventBusMiddleware($logger),
            // new ProducerEventBusMiddleware($this->producer)
            new EventBusMiddleware($this->mapper, $this->resolver)
        ];

        $middleware2 = [
            new LoggerEventBusMiddleware($logger),
            new ProducerEventBusMiddleware($this->doctrineQueue)
            // new EventBusMiddleware($this->mapper, $this->resolver)
        ];

        $eventBus = new EventBus($middleware);
        $eventBus1 = new EventBus($middleware1);
        $eventBus2 = new EventBus($middleware2);

        for ($i = 1; $i <= 1000; ++ $i) {
            // $eventBus->__invoke(new DummyEvent());
            // $eventBus1->__invoke(new DummyEvent());
            $eventBus2->__invoke(new DummyEvent());
        }

        $this->assertNotEmpty($logger);
    }
}