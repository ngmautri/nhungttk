<?php
namespace Procure\Application\Event\Handler\PR;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Pr\PrRowAdded;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowAddedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PrRowAdded::class => 'onRowAdded'
        ];
    }

    /**
     *
     * @param PrRowAdded $ev
     */
    public function onRowAdded(PrRowAdded $ev)
    {
        /**
         *
         * @var PRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();
        if ($rootSnapshot == null) {
            throw new OperationFailedException("PRSnapshot not give at ApRowAddedHandler");
        }

        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());

        $class = new \ReflectionClass($rootSnapshot);
        $className = null;

        if ($class !== null) {
            $className = $class->getShortName();
        }

        $params = $ev->getParams();
        $trigger = $ev->getTrigger();

        $rowId = null;
        if (isset($params['rowId'])) {
            $rowId = $params['rowId'];
        }

        $rowToken = null;
        if (isset($params['rowToken'])) {
            $rowToken = $params['rowToken'];
        }

        $changeLog1 = array();

        $changeLog1['rowId'] = $rowId;
        $changeLog1['rowToken'] = $rowToken;

        $message = new MessageStore();

        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getDocVersion());

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.pr");

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setClassName($className);
        $message->setTriggeredBy($ev->getTrigger());
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootSnapshot));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}