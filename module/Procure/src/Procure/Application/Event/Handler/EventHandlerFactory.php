<?php
namespace Procure\Application\Event\Handler;

use Doctrine\ORM\EntityManager;
use Procure\Application\Event\Handler\AP\ApPostedHandler;
use Procure\Application\Event\Handler\GR\GrFromApPosted;
use Procure\Application\Event\Handler\GR\GrPostedHandler;
use Procure\Application\Event\Handler\PO\PoAmendmentAcceptedHandler;
use Procure\Application\Event\Handler\PO\PoAmendmentEnabledHandler;
use Procure\Application\Event\Handler\PO\PoHeaderCreatedHandler;
use Procure\Application\Event\Handler\PO\PoHeaderUpdatedHandler;
use Procure\Application\Event\Handler\PO\PoPostedHandler;
use Procure\Application\Event\Handler\PO\PoRowAddedHandler;
use Procure\Application\Event\Handler\PO\PoRowUpdatedHandler;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EventHandlerFactory
{

    public static function createEventHandler($eventName, EntityManager $doctrineEM = null)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $handlers = array();
        switch ($eventName) {
            case PoHeaderCreated::class:
                $handlers[] = new PoHeaderCreatedHandler($doctrineEM);
                break;

            case PoHeaderUpdated::class:
                $handlers[] = new PoHeaderUpdatedHandler($doctrineEM);
                break;

            case PoRowAdded::class:
                $handlers[] = new PoRowAddedHandler($doctrineEM);
                break;

            case PoRowUpdated::class:
                $handlers[] = new PoRowUpdatedHandler($doctrineEM);
                break;

            case PoPosted::class:
                $handlers[] = new PoPostedHandler($doctrineEM);
                break;
            case PoAmendmentEnabled::class:
                $handlers[] = new PoAmendmentEnabledHandler($doctrineEM);
                break;

            case PoAmendmentAccepted::class:
                $handlers[] = new PoAmendmentAcceptedHandler($doctrineEM);
                break;

            case ApPosted::class:
                $handlers[] = new ApPostedHandler($doctrineEM);
                $handlers[] = new GrFromApPosted($doctrineEM);
                break;

            case GrPosted::class:
                $handlers[] = new GrPostedHandler($doctrineEM);

                break;
        }

        return $handlers;
    }
}