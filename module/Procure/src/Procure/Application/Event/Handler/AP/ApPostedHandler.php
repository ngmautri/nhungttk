<?php
namespace Procure\Application\Event\Handler\AP;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApPostedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ApPosted::class => 'onPosted'
        ];
    }

    /**
     *
     * @param ApPosted $ev
     */
    public function onPosted(ApPosted $ev)
    {

        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if ($rootSnapshot == null) {
            return;
        }

        $params = $ev->getParams();
        $trigger = $ev->getTrigger();

        $class = new \ReflectionClass($rootSnapshot);
        $className = null;

        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $queryRep = new APQueryRepositoryImpl($this->getDoctrineEM());

        // time to check version - concurency
        $verArray = $queryRep->getVersionArray($rootSnapshot->getId());
        $currentRevisionNo = null;
        $currentVersion = null;

        if ($verArray != null) {
            if (isset($verArray["docVersion"])) {
                $currentVersion = $verArray["docVersion"];
            }

            if (isset($verArray["revisionNo"])) {
                $currentRevisionNo = $verArray["revisionNo"];
            }
        }

        $message->setRevisionNo($currentRevisionNo);
        $message->setVersion($currentVersion);

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.ap");
        $message->setChangeLog(sprintf("AP Invoice #%s is %s!", $rootSnapshot->getId(), $rootSnapshot->getDocStatus()));
        $message->setClassName($className);
        $message->setTriggeredBy($ev->getTrigger());
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootSnapshot));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);

        // Create PO message

        $rows = $rootSnapshot->getDocRows();
        if (count($rows) > 0) {

            foreach ($rows as $row) {
                /**
                 *
                 * @var APRow $row ;
                 */
                if ($row->getPoRow() > 0) {
                    $message = new MessageStore();
                    $message->setEntityId($row->getPoId());
                    $message->setEntityToken($row->getPoToken());
                    $message->setQueueName("procure.ap");
                    $message->setChangeLog(sprintf("AP for PO #%s is %s!", $row->getPoId(), $rootSnapshot->getDocStatus()));
                    $message->setClassName($className);
                    $message->setTriggeredBy($ev->getTrigger());
                    $message->setUuid(Uuid::uuid4());
                    $message->setMsgBody("");
                    $message->setCreatedOn(new \DateTime());
                    $message->setEventName(get_class($ev));
                    $this->getDoctrineEM()->persist($message);
                }
            }
        }

        $this->getDoctrineEM()->flush();
    }
}