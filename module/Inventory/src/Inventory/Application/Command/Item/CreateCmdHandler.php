<?php
namespace Inventory\Application\Command\Item;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Service\ItemPostingService;
use Inventory\Domain\Service\SharedService;
use Inventory\Infrastructure\Doctrine\ItemCmdRepositoryImpl;
use Procure\Application\Service\FXService;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateCmdHandler extends AbstractCommandHandler
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
         * @var ItemDTO $dto ;
         * @var CreateItemOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof CreateItemOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {

            $notification = new Notification();
            $snapshot = $dto;

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());

            $cmdRepository = new ItemCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new ItemPostingService($cmdRepository);

            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);

            $rootEntity = ItemFactory::createFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Item Created!", $rootEntity->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
