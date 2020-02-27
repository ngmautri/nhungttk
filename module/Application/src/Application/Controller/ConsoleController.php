<?php
namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Application\Infrastructure\Doctrine\MessageStoreRepository;

class ConsoleController extends AbstractActionController
{

    protected $doctrineEM;

    private $host = 'zebra.rmq.cloudamqp.com';

    private $post = 5672;

    private $user = 'roblgcxy';

    private $pass = 'dfnRxpAByZYpmxCTA-g_r_u1zqkXoViP';

    private $vhost = 'roblgcxy';

    /**
     *
     * php "D:\Software Development\php-2019-12-R\mla-2.6.7\public\index.php send-to-rabitmq"
     * create enviroment varibles: C:\Program Files\MySQL\MySQL Server 5.7\bin
     * Mysql client need to be installed.
     *
     * @return \zend\stdlib\responseinterface|\zend\view\model\viewmodel
     */
    public function sendToRabitMQAction()
    {
        $rep = new MessageStoreRepository($this->getDoctrineEM());
        $results = $rep->getUnsentMessage();
        if ($results == null) {
            echo  "no thing sent!";
            
            return;
        }
        
        $sentIDs = array();

        try {
            // send send massage to RabbitQP
            $connection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);
            $channel = $connection->channel();
            $n = 0;
            foreach ($results as $result) {

                /**
                 *
                 * @var \Application\Entity\MessageStore $result ;
                 */
                $n ++;
                $m = $result->getMsgBody();
                $binding_key = $result->getQueueName();
                $channel->queue_declare($binding_key, false, false, false, false);

                $msg = new AMQPMessage($m);
                $channel->basic_publish($msg, '', $binding_key);
                
                $sentIDs[] = $result->getId();
            }

            $channel->close();
            $connection->close();
            
            $rep->setSentDate($sentIDs);

            echo $n . " messages sent!";
            
            //sleep(20);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
   
    public function receiveMsgAction()
    {
        // send send massage to RabbitQP
        $connection = new AMQPStreamConnection($this->host, $this->post, $this->user, $this->pass, $this->vhost);
        $channel = $connection->channel();
        $channel->queue_declare('inventory.item', false, false, false, false);
        
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
        
        $channel->basic_consume('inventory.item', '', false, true, false, false, $callback);
        
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        
        $channel->close();
        $connection->close();
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
