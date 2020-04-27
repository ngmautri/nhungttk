<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoRowCreateException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\Validator\DefaultHeaderValidator;
use Procure\Domain\PurchaseOrder\Validator\DefaultRowValidator;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;

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
            throw new PoRowCreateException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof PORowDetailsDTO) {
            throw new PoRowCreateException("PORowDetailsDTO object not found!");
        }

        if (! $cmd->getOptions() instanceof PoRowCreateOptions) {
            throw new PoRowCreateException("Cmd Options object not found!");
        }

        /**
         *
         * @var PoDTO $dto ;
         * @var PoRowCreateOptions $options ;
         * @var PORowSnapshot $snapshot ;
         * @var PODoc $rootEntity ;
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

            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new PORowSnapshot());

            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $sharedService = new SharedService($sharedSpecificationFactory, $fxService);

            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecificationFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecificationFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new POCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);

            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatcher
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("[OK] PO Row # %s created", $localSnapshot->getId());
            $notification->addSuccess($m);

            $queryRep = new POQueryRepositoryImpl($cmd->getDoctrineEM());

            // revision numner has been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
