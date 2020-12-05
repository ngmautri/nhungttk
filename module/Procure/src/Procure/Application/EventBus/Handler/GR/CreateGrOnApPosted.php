<?php
namespace Procure\Application\EventBus\Handler\GR;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Procure\Application\Command\Doctrine\GR\PostCopyFromAPCmdHandler;
use Procure\Application\Command\Options\PostCopyFromCmdOptions;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateGrOnApPosted extends AbstractEventHandler
{

    public function __invoke(ApPosted $ev)
    {
        /**
         *
         * @var APSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new \InvalidArgumentException(\sprintf("Can not retrtrive AP Documment", " GrFromApPosted"));
        }

        $id = $rootSnapshot->getId();
        $token = $rootSnapshot->getToken();

        $rep = new APQueryRepositoryImpl($this->getDoctrineEM());
        $rootEntity = $rep->getRootEntityByTokenId($id, $token);

        $rep1 = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $companyVO = $rep1->getById($rootEntity->getCompany())
            ->createValueObject();

        $options = new PostCopyFromCmdOptions($rootSnapshot->getCompany(), $rootEntity->getCreatedBy(), __METHOD__, $companyVO);

        $cmdHandler = new PostCopyFromAPCmdHandler();
        $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHandler, $this->getEventBusService());
        $cmd->execute();

        $this->logInfo(\sprintf("GR created from AP!  #%s ", $ev->getTarget()
            ->getId()));
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