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
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use InvalidArgumentException;

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
            $fifoService->setLogger($cmd->getLogger());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $sharedService->setValuationService($valuationService);
            $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($cmd->getDoctrineEM()));
            $sharedService->setLogger($cmd->getLogger());

            $rootEntity->post($options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Trx #%s posted", $rootEntity->getId());
            $notification->addSuccess($m);

            $queryRep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
