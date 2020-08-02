<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\TrxPostOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Service\SharedService;
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
         * @var TrxDTO $dto ;
         * @var TrxDoc $rootEntity ;
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxPostOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof TrxPostOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof TrxDTO) {
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

            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $cmdRepository = new TrxCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new TrxPostingService($cmdRepository);

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($cmd->getDoctrineEM());
            $valuationService = new TrxValuationService($fifoService);

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $sharedService->setValuationService($valuationService);
            $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($cmd->getDoctrineEM()));

            $rootEntity->post($options, $sharedService);

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
            throw new OperationFailedException($e->getMessage());
        }
    }
}
