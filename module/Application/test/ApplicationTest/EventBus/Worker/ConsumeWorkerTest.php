<?php
namespace ApplicationTest\EventBus\Worker;

use ApplicationTest\EventBus\DummyEvent2Handler;
use ApplicationTest\EventBus\DummyEventHandler;
use Application\Domain\EventBus\EventBus;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Domain\EventBus\Handler\Resolver\SimpleArrayResolver;
use Application\Domain\EventBus\Middleware\EventBusMiddleware;
use Application\Domain\EventBus\Middleware\ProducerEventBusMiddleware;
use Application\Domain\EventBus\Queue\AmqpQueue;
use Application\Domain\EventBus\Queue\FileSystemQueue;
use Application\Domain\EventBus\Queue\Worker\SimpleConsumeWorker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit_Framework_TestCase;

class ConsumerWorkerTest extends PHPUnit_Framework_TestCase
{

    /** @var string */
    protected $dirPath;

    /** @var array */
    protected $handlers;

    protected $mapper;

    protected $resolver;

    /** @var EventBus */
    protected $consumerEventBus;

    /** @var EventBus */
    protected $producerEventBus;

    /** @var FileSystemQueue */
    protected $consumerQueue;

    /** @var FileSystemQueue */
    protected $errorQueue;

    /** @var string */
    protected $queueName = 'testQueueName';

    private $host = 'zebra.rmq.cloudamqp.com';

    private $post = 5672;

    private $user = 'roblgcxy';

    private $pass = 'dfnRxpAByZYpmxCTA-g_r_u1zqkXoViP';

    private $vhost = 'roblgcxy';

    public function setUp()
    {
        $this->handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },

            DummyEvent2Handler::class => function () {
                return new DummyEvent2Handler();
            }
        ];

        $this->mapper = new FullNameHandlerMapper([
            DummyEventHandler::class,
            DummyEvent2Handler::class
        ]);

        $this->resolver = new SimpleArrayResolver($this->handlers);

        $this->setUpQueue();

        $this->producerEventBus = new EventBus([
            new ProducerEventBusMiddleware($this->consumerQueue),
            new EventBusMiddleware($this->mapper, $this->resolver)
        ]);

        $this->consumerEventBus = new EventBus([
            new EventBusMiddleware($this->mapper, $this->resolver)
        ]);
    }

    protected function setUpQueue()
    {
        $path = __DIR__ . '/errored-jobs';

        if (false === file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->consumerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);
        // $this->producerConnection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);

        $this->consumerQueue = new AmqpQueue($this->consumerConnection, $this->queueName);

        // $this->consumerQueue = new FileSystemQueue($this->serializer, __DIR__ . '/jobs', $this->queueName);
        $this->errorQueue = new FileSystemQueue(__DIR__ . '/errored-jobs', $this->queueName);
    }

    public function testItCanConsume()
    {
        /*
         * for ($i = 1; $i <= 10; ++ $i) {
         * // $this->producerEventBus->__invoke(new DummyEvent());
         * }
         */
        $consumer = new SimpleConsumeWorker();
        $consumer->consume($this->consumerQueue, $this->errorQueue, $this->consumerEventBus);
    }

    /*
     * public function testItCanCatchExceptionWhileConsume()
     * {
     * /*
     * for ($i = 1; $i <= 10; ++ $i) {
     * $this->producerEventBus->__invoke(new DummyEvent());
     * }
     *
     * $this->consumerEventBus = new EventBus([
     * new ThrowsExceptionEventBusMiddleware(),
     * new EventBusMiddleware($this->translator, $this->resolver)
     * ]);
     *
     * $consumer = new SimpleConsumeWorker();
     * $consumer->consume($this->consumerQueue, $this->errorQueue, $this->consumerEventBus);
     *
     * }
     */
    public function tearDown()
    {
        /*
         * $this->emptyDir(__DIR__ . '/errored-jobs');
         * $this->emptyDir(__DIR__ . '/jobs');
         */
    }

    protected function emptyDir($path)
    {
        if (file_exists($path)) {
            foreach (\glob($path . '/*') as $file) {
                unlink($file);
            }
        }
        rmdir($path);
    }
}