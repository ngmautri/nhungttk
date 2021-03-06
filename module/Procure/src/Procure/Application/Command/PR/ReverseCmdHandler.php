<?php
namespace Procure\Application\Command\PR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApReverseOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Header\ReversalValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\GLAccountValidator;
use Procure\Domain\AccountPayable\Validator\Row\PoRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\WarehouseValidator;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReverseCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var APDoc $rootEntity ;
         * @var APSnapshot $rootSnapshot ;
         * @var ApReverseOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof ApReverseOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof ApDTO) {
            throw new InvalidArgumentException(sprintf("DTO object not found! %s", __FUNCTION__));
        }

        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();
        $trigger = $options->getTriggeredBy();

        try {

            $snapshot = new APSnapshot();
            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, $snapshot);

            $notification = new Notification();
            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $procureSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $headerValidators = new HeaderValidatorCollection();

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new ReversalValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();

            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new PoRowValidator($sharedSpecFactory, $fxService, $procureSpecsFactory);
            $rowValidators->add($validator);

            $validator = new WarehouseValidator($sharedSpecFactory, $fxService, $procureSpecsFactory);
            $rowValidators->add($validator);

            $validator = new GLAccountValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new APCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new APPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $reversalEntity = APDoc::createAndPostReserval($rootEntity, $snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("AP #%s reversed with #%s", $rootEntity->getId(), $reversalEntity->getId());
            $notification->addSuccess($m);

            $queryRep = new APQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
