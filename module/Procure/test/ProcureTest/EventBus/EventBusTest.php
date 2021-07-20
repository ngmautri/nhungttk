<?php
namespace ProcureTest\EventBus;

use Application\Application\EventBus\PsrHandlerResolver;
use Application\Domain\EventBus\Queue\AmqpQueue;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use ProcureTest\Bootstrap;
use Procure\Application\EventBus\EventBusService;
use Procure\Application\EventBus\HandlerMapper;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use PHPUnit_Framework_TestCase;

class EventBusTest extends PHPUnit_Framework_TestCase
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

    /**
     *
     * @var EventBusService
     */
    protected $eventBusService;

    public function setUp()
    {
        $container = Bootstrap::getServiceManager();
        $doctrineEM = $container->get('doctrine.entitymanager.orm_default');

        $this->resolver = $container->get(PsrHandlerResolver::class);
        $this->mapper = $container->get(HandlerMapper::class);
        $this->eventBusService = $container->get(EventBusService::class);
    }

    public function testItCanStackMiddleware()
    {
        $params = [
            "changeLog" => [
                "newValue" => "12"
            ]
        ];

        $entityId = 2825;
        $entityToken = "ec82eb88-f1ad-4dfa-b4bd-cff6f76a1816";

        $events = [
            new ApHeaderUpdated(new APSnapshot(), $entityId, $entityToken, 1, 1, __METHOD__, $params)
            // new PoPosted(new POSnapshot()),
            // new PrPosted(new PRSnapshot())
        ];

        $this->eventBusService->dispatch($events);
    }
}