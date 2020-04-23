<?php
namespace Procure\Application\Event\Handler;

use Doctrine\ORM\EntityManager;
use Procure\Application\Event\Handler\AP\ApHeaderUpdatedHandler;
use Procure\Application\Event\Handler\AP\ApPostedHandler;
use Procure\Application\Event\Handler\AP\ApReversedHandler;
use Procure\Application\Event\Handler\AP\ApRowAddedHandler;
use Procure\Application\Event\Handler\AP\ApRowUpdatedHandler;
use Procure\Application\Event\Handler\GR\GrFromApPosted;
use Procure\Application\Event\Handler\GR\GrFromApReversalPosted;
use Procure\Application\Event\Handler\GR\GrPostedHandler;
use Procure\Application\Event\Handler\GR\GrReversedHandler;
use Procure\Application\Event\Handler\PO\PoAmendmentAcceptedHandler;
use Procure\Application\Event\Handler\PO\PoAmendmentEnabledHandler;
use Procure\Application\Event\Handler\PO\PoHeaderCreatedHandler;
use Procure\Application\Event\Handler\PO\PoHeaderUpdatedHandler;
use Procure\Application\Event\Handler\PO\PoPostedHandler;
use Procure\Application\Event\Handler\PO\PoRowAddedHandler;
use Procure\Application\Event\Handler\PO\PoRowUpdatedHandler;
use Procure\Application\Event\Handler\PR\PrHeaderCreatedHandler;
use Procure\Application\Event\Handler\PR\PrHeaderUpdatedHandler;
use Procure\Application\Event\Handler\PR\PrRowAddedHandler;
use Procure\Application\Event\Handler\PR\PrRowUpdatedHandler;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Event\Ap\ApRowAdded;
use Procure\Domain\Event\Ap\ApRowUpdated;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;
use Procure\Domain\Event\Pr\PrHeaderCreated;
use Procure\Domain\Event\Pr\PrHeaderUpdated;
use Procure\Domain\Event\Pr\PrRowAdded;
use Procure\Domain\Event\Pr\PrRowUpdated;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventHandlerFactory
{

    public $handlers = [
        PrRowAdded::class => []
    ];

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

            case ApHeaderUpdated::class:
                $handlers[] = new ApHeaderUpdatedHandler($doctrineEM);
                break;
            case ApRowAdded::class:
                $handlers[] = new ApRowAddedHandler($doctrineEM);
                break;
            case ApRowUpdated::class:
                $handlers[] = new ApRowUpdatedHandler($doctrineEM);
                break;

            case ApReversed::class:
                $handlers[] = new ApReversedHandler($doctrineEM);
                $handlers[] = new GrFromApReversalPosted($doctrineEM);

                break;

            case GrPosted::class:
                $handlers[] = new GrPostedHandler($doctrineEM);

            case GrReversed::class:
                $handlers[] = new GrReversedHandler($doctrineEM);

            case PrHeaderCreated::class:
                $handlers[] = new PrHeaderCreatedHandler($doctrineEM);
            case PrHeaderUpdated::class:
                $handlers[] = new PrHeaderUpdatedHandler($doctrineEM);

            case PrRowAdded::class:
                $handlers[] = new PrRowAddedHandler($doctrineEM);

            case PrRowUpdated::class:
                $handlers[] = new PrRowUpdatedHandler($doctrineEM);
                break;
        }

        return $handlers;
    }
}