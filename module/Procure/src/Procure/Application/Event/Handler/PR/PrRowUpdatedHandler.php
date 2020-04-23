<?php
namespace Procure\Application\Event\Handler\PR;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Pr\PrRowUpdated;
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
class PrRowUpdatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PrRowUpdated::class => 'onRowUpdated'
        ];
    }

    /**
     *
     * @param PrRowUpdated $ev
     */
    public function onRowUpdated(PrRowUpdated $ev)
    {
        /**
         *
         * @var PRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();
        if ($rootSnapshot == null) {
            throw new OperationFailedException(" RootSnapshot not give at ApRowAddedHandler");
        }

        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());

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
        $message->setQueueName("procure.pr");

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