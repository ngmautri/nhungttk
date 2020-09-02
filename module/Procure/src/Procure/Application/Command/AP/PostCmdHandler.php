<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApPostOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PostCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not foundsv!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var APDoc $rootEntity ;
         * @var APSnapshot $rootSnapshot ;
         * @var ApPostOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof ApPostOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof ApDTO) {
            throw new InvalidArgumentException("DTO object not found!");
        }

        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();
        $trigger = $options->getTriggeredBy();

        try {

            $notification = new Notification();

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $postingService = new APPostingService(new APCmdRepositoryImpl($cmd->getDoctrineEM()));
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $domainSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

            $validationService = ValidatorFactory::createForPosting($sharedService, true);

            $rootEntity->post($options, $validationService, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("AP #%s posted", $rootEntity->getId());
            $notification->addSuccess($m);

            $queryRep = new APQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
