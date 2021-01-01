<?php
namespace Inventory\Application\Command\Item;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Service\ItemPostingService;
use Inventory\Domain\Service\SharedService;
use Inventory\Infrastructure\Doctrine\ItemCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use InvalidArgumentException;
use Inventory\Application\Command\VersionChecker;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var ItemDTO $dto ;
         * @var UpdateItemOptions $options ;
         * @var GenericItem $rootEntity ;
         */

        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();

        if (! $options instanceof UpdateItemOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {

            $notification = new Notification();

            /**
             *
             * @var ItemSnapshot $snapshot ;
             * @var ItemSnapshot $newSnapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            // not allow to change item type in this command.
            $excludedProperties = [
                "itemTypeId"
            ];

            $newSnapshot = ItemSnapshotAssembler::updateSnapshotFromDTOExcludeFields($newSnapshot, $dto, $excludedProperties);

            $excludedProps = [
                "qoList",
                "procureGrList",
                "batchNoList",
                "fifoLayerConsumeList",
                "stockGrList",
                "pictureList",
                "attachmentList",
                "prList",
                "poList",
                "apList",
                "serialNoList",
                "batchList",
                "fifoLayerList",
                "backwardAssociationList",
                "associationList"
            ];
            $changeLog = $snapshot->compareExcludedFields($newSnapshot, $excludedProps);

            if ($changeLog == null) {
                $notification->addError("Nothing change on Item #" . $rootEntityId);
                $dto->setNotification($notification);
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $cmdRepository = new ItemCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new ItemPostingService($cmdRepository);
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);

            $newRootEntity = ItemFactory::updateFrom($rootEntity, $newSnapshot, $options, $params, $sharedService);

            VersionChecker::checkItemVersion($cmd->getDoctrineEM(), $rootEntityId, $version);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($newRootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Item updated!", $newRootEntity->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
