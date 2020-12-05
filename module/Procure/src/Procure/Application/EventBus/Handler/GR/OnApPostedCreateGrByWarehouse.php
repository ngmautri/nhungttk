<?php
namespace Procure\Application\EventBus\Handler\GR;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Procure\Application\Command\Doctrine\GR\PostCopyFromAPByWarehouseCmdHandler;
use Procure\Application\Command\Options\PostCopyFromCmdOptions;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

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

        $rep1 = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $company = $rep1->getById($rootEntity->getCompany());

        $options = new PostCopyFromCmdOptions($company->createValueObject(), $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

        $cmdHandler = new PostCopyFromAPByWarehouseCmdHandler();
        $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHandler, $this->getEventBusService());
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