<?php
namespace Inventory\Application\Command\Warehouse;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Warehouse\Options\WhCreateOptions;
use Inventory\Application\DTO\Warehouse\WarehouseDTO;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\WarehousePostingService;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\Factory\WarehouseFactory;
use Inventory\Infrastructure\Doctrine\WhCmdRepositoryImpl;
use Procure\Application\Service\FXService;
use InvalidArgumentException;
use Application\Application\Service\Shared\FXServiceImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateWarehouseCmdHandler extends AbstractCommandHandler
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
         * @var WarehouseDTO $dto ;
         * @var WhCreateOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof WhCreateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {

            $dto->company = $options->getCompanyId();
            $dto->createdBy = $options->getUserId();

            /**
             *
             * @var WarehouseSnapshot $snapshot ;
             * @var WarehouseSnapshot $rootSnapshot ;
             * @var GenericWarehouse $rootEntity ;
             */
            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new WarehouseSnapshot());
            // \var_dump($snapshot);

            $notification = new Notification();

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());

            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $cmdRepository = new WhCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new WarehousePostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $rootEntity = WarehouseFactory::createFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Warehouse created %s!", $rootEntity->getId());
            $notification->addSuccess($m);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
