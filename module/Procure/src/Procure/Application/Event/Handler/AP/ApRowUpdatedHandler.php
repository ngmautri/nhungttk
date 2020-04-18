<?php
namespace Procure\Application\Event\Handler\AP;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApRowUpdated;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApRowUpdatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ApRowUpdated::class => 'onRowUpdated'
        ];
    }

    /**
     *
     * @param ApRowUpdated $ev
     */
    public function onRowUpdated(ApRowUpdated $ev)
    {
        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();
        if ($rootSnapshot == null) {
            return;
        }

        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());

        // time to check version - concurency
        $verArray = $rep->getVersionArray($rootSnapshot->getId());
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

        $changeLog = null;
        $changeLog1 = array();
        $changeLog_tmp = array();

        $changeLog1['rowId'] = $rowId;
        $changeLog1['rowToken'] = $rowToken;

        if (isset($params['changeLog'])) {
            $changeLog = $params['changeLog'];

            if (count($changeLog) > 0) {

                foreach ($changeLog as $k => $v) {

                    $changeLog2 = array();
                    foreach ($v as $k1 => $v1) {
                        if ($k1 == "className") {
                            continue;
                        }

                        $changeLog2[] = [
                            $k1 => $v1
                        ];
                        ;
                    }

                    $changeLog_tmp[] = $changeLog2;
                }
            }
        }

        $changeLog1['changeLog'] = $changeLog_tmp;

        $message = new MessageStore();

        $message->setRevisionNo($currentRevisionNo);
        $message->setVersion($currentVersion);
        $message->setTriggeredBy($trigger);

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.ap");

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setClassName($className);
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootSnapshot));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}