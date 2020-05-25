<?php
namespace Procure\Application\EventBus\Handler\GR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\GR\PostCopyFromAPReservalCmdHandler;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApReservalCreated;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateGrReversalOnApReversed extends AbstractEventHandler
{

    /**
     *
     * @param ApReservalCreated $ev
     * @throws \InvalidArgumentException
     */
    public function __invoke(ApReservalCreated $ev)
    {
        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new \InvalidArgumentException(\sprintf("Can not retrtrive AP Documment", " CreateGrReversalOnApReversed"));
        }

        $id = $rootSnapshot->getId();
        $token = $rootSnapshot->getToken();

        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);
        $options = new PostCopyFromAPOptions($rootSnapshot->getCompany(), $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

        $dto = new GrDTO();
        $cmdHandler = new PostCopyFromAPReservalCmdHandler();
        $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandler, $this->getEventBusService());
        $cmd->execute();

        $this->getLogger()->info(\sprintf("GR created from AP Reversal!  #%s ", $ev->getTarget()
            ->getId()));
    }

    public static function priority()
    {
        return 100;
    }

    public static function subscribedTo()
    {
        return ApReservalCreated::class;
    }
}