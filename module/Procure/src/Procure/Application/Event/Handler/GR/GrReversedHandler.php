<?php
namespace Procure\Application\Event\Handler\GR;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReversedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            GrReversed::class => 'onReversed'
        ];
    }

    /**
     *
     * @param GrReversed $ev
     */
    public function onReversed(GrReversed $ev)
    {

        /**
         *
         * @var GRSnapshot $rootSnapshot ;
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

        $queryRep = new GRQueryRepositoryImpl($this->getDoctrineEM());

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
        $message->setQueueName("procure.gr");
        $message->setChangeLog(sprintf("G/R #%s is %s!", $rootSnapshot->getId(), $rootSnapshot->getDocStatus()));
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
                 * @var GRRow $row ;
                 */
                if ($row->getPoRow() > 0) {
                    $message = new MessageStore();
                    $message->setEntityId($row->getPoId());
                    $message->setEntityToken($row->getPoToken());
                    $message->setQueueName("procure.gr");
                    $message->setChangeLog(sprintf("GR #%s for PO #%s is %s!", $rootSnapshot->getId(), $row->getPoId(), $rootSnapshot->getDocStatus()));
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