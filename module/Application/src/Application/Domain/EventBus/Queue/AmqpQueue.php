<?php
namespace Application\Domain\EventBus\Queue;

use Application\Domain\EventBus\Event\EventInterface;
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

    /**
     * AsyncAmqpEventBusMiddleware constructor.
     *
     * @param AMQPStreamConnection $streamConnection
     * @param string $queueName
     */
    public function __construct(AMQPStreamConnection $streamConnection, string $queueName)
    {
        $this->amqpChannel = $streamConnection->channel();
        $this->queueName = $queueName;
    }

    public function pop()
    {
        $this->declareQueue();
        $message = $this->amqpChannel->basic_get($this->queueName);

        if (! empty($message)) {
            $this->amqpChannel->basic_ack($message->delivery_info['delivery_tag']);
        }

        return ($message) ? $this->serializer->unserialize($message->body) : \NullEvent::create();
    }

    public function hasElements()
    {}

    public function name()
    {
        return $this->queueName;
    }

    public function push(EventInterface $event)
    {
        $this->declareQueue();
        $this->amqpChannel->basic_publish(new AMQPMessage($this->serializer->serialize($event), [
            'delivery_mode' => 2
        ]), '', $this->queueName, true);
    }
}