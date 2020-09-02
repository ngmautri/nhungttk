<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApRowCreateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Application\Service\AP\RowSnapshotReference;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRowSnapshot;
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
class AddRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof ApRowDTO) {
            throw new InvalidArgumentException("ApRowDTO object not found!");
        }

        if (! $cmd->getOptions() instanceof ApRowCreateOptions) {
            throw new InvalidArgumentException("Cmd Options object not found!");
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var InvalidArgumentException $options ;
         * @var APRowSnapshot $snapshot ;
         * @var APDoc $rootEntity ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        $notification = new Notification();

        $userId = $options->getUserId();
        $rootEntity = $options->getRootEntity();
        $version = $options->getVersion();

        try {

            $dto->createdBy = $userId;
            $dto->company = $rootEntity->getCompany();

            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new APRowSnapshot());
            $snapshot = RowSnapshotReference::updateReferrence($snapshot, $cmd->getDoctrineEM());

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $postingService = new APPostingService(new APCmdRepositoryImpl($cmd->getDoctrineEM()));
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $domainSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

            $validationService = ValidatorFactory::create($sharedService);

            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $validationService, $sharedService);

            // event dispatch
            // event dispatcher
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("[OK] Row # %s created", $localSnapshot->getId());
            $notification->addSuccess($m);

            $queryRep = new APQueryRepositoryImpl($cmd->getDoctrineEM());

            $dto->setNotification($notification);

            // revision numner has been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
