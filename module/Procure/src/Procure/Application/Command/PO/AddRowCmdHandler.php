<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoRowCreateException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
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

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

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
