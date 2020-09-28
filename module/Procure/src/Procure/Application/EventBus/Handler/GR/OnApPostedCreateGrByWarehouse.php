<?php
namespace Procure\Application\EventBus\Handler\GR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\GR\PostCopyFromAPCmdHandler;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Application\Command\GR\PostCopyFromAPByWarehouseCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnApPostedCreateGrByWarehouse extends AbstractEventHandler
{

    public function __invoke(ApPosted $ev)
    {
        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new \RuntimeException(\sprintf("Can not retrtrive AP Documment", " GrFromApPosted"));
        }

        $id = $rootSnapshot->getId();
        $token = $rootSnapshot->getToken();

        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        $options = new PostCopyFromAPOptions($rootEntity->getCompany(), $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

        $dto = new GrDTO();
        $cmdHandler = new PostCopyFromAPByWarehouseCmdHandler();
        $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandler, $this->getEventBusService());
        $cmd->setLogger($this->getLogger());
        $cmd->execute();
    }

    public static function priority()
    {
        return 100;
    }

    public static function subscribedTo()
    {
        return ApPosted::class;
    }
}