<?php
namespace Procure\Application\EventBus\Handler\QR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Application\Service\PR\PRService;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchIndexImpl;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Qr\QrPosted;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateIndexOnQrPosted extends AbstractEventHandler
{

    /**
     *
     * @param PoPosted $event
     */
    public function __invoke(QrPosted $event)
    {
        $indexer = new QrSearchIndexImpl();
        $rep = new QRQueryRepositoryImpl($this->getDoctrineEM());

        $entity = $rep->getRootEntityByTokenId($event->getTarget()
            ->getId(), $event->getTarget()
            ->getToken());

        $indexer->setLogger($this->getLogger());
        $indexer->createDoc($entity->makeSnapshot());

        $this->logInfo(\sprintf("Search index for QR#%s created!", $entity->getId()));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return QrPosted::class;
    }
}