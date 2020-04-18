<?php
namespace Procure\Application\Event\Handler\AP;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApRowAdded;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApRowAddedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ApRowAdded::class => 'onRowAdded'
        ];
    }

    /**
     *
     * @param ApRowAdded $ev
     */
    public function onRowAdded(ApRowAdded $ev)
    {
        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();
        if ($rootSnapshot == null) {
            throw new OperationFailedException("APSnapshot not give at ApRowAddedHandler");
        }

        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());

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
        $message->setQueueName("procure.ap");

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