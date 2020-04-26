<?php
namespace Application\Domain\EventBus\Queue;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Event\NullEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AmqpQueue implements QueueInterface
{

    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    protected $amqpChannel;

    /** @var string */
    protected $queueName;

    /** @var bool */
    protected $isDeclared = false;

    protected $connection;

    /**
     * AsyncAmqpEventBusMiddleware constructor.
     *
     * @param AMQPStreamConnection $streamConnection
     * @param string $queueName
     */
    public function __construct(AMQPStreamConnection $streamConnection, $queueName)
    {
        $this->connection = $streamConnection;
        $this->amqpChannel = $streamConnection->channel();
        $this->queueName = $queueName;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::pop()
     */
    public function pop()
    {
        $this->declareQueue();
        $message = $this->amqpChannel->basic_get($this->queueName);

        if (! empty($message)) {
            $this->amqpChannel->basic_ack($message->delivery_info['delivery_tag']);
        }

        return ($message) ? \unserialize($message->body) : NullEvent::create();
    }

    public function hasElements()
    {}

    public function name()
    {
        return $this->queueName;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::push()
     */
    public function push(EventInterface $event)
    {
        $this->declareQueue();
        /*
         * $this->amqpChannel->basic_publish(new AMQPMessage(json_encode($event), [
         * 'delivery_mode' => 2
         * ]), '', $this->queueName, true);
         */

        /*
         * $arr = array(
         * 'a' => 1,
         * 'b' => 2,
         * 'c' => 3,
         * 'd' => 4,
         * 'e' => 5
         * );
         */
        // $body = json_encode((array) $event);

        $body = \serialize($event);

        $msg = new AMQPMessage($body);
        $this->amqpChannel->basic_publish($msg, '', $this->queueName);

        echo " [x] Sent 'Hello World!'\n";
        // $this->amqpChannel->close();
        // $this->connection->close();
    }

    /**
     */
    protected function declareQueue()
    {
        if (false === $this->isDeclared) {
            $this->amqpChannel->queue_declare($this->queueName, false, false, false, false);
            $this->isDeclared = true;
        }
    }
}