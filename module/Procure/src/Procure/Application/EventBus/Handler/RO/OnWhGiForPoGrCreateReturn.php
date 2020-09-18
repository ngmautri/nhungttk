<?php
namespace Procure\Application\EventBus\Handler\RO;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Inventory\Domain\Transaction\TrxSnapshot;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\GR\PostCopyFromAPCmdHandler;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Inventory\Domain\Event\Transaction\GI\WhGiForPoGrPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhGiForPoGrCreateReturn extends AbstractEventHandler
{

    public function __invoke(WhGiForPoGrPosted $ev)
    {
        /**
         *
         * @var TrxSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new OperationFailedException(\sprintf("Can not retrtrive AP Documment", " GrFromApPosted"));
        }

        $this->logInfo(\sprintf("GR created from AP!  #%s ", $ev->getTarget()
            ->getId()));
    }

    public static function priority()
    {
        return 100;
    }

    public static function subscribedTo()
    {
        return WhGiForPoGrPosted::class;
    }
}