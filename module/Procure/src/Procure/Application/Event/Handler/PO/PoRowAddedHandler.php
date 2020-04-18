<?php
namespace Procure\Application\Event\Handler\PO;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoRowAddedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoRowAdded::class => 'onPoRowAdded'
        ];
    }

    /**
     *
     * @param PoRowAdded $ev
     */
    public function onPoRowAdded(PoRowAdded $ev)
    {
        $rep = new POQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getHeaderById($ev->getTarget());

        $class = new \ReflectionClass($rootEntity);
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

        $message->setRevisionNo($rootEntity->getRevisionNo());
        $message->setVersion($rootEntity->getDocVersion());

        $message->setEntityId($ev->getTarget());
        $message->setEntityToken($rootEntity->getToken());
        $message->setQueueName("procure.po");

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setClassName($className);
        $message->setTriggeredBy($ev->getTrigger());
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootEntity->makeSnapshot()));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}