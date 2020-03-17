<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Exception\PoVersionChangedException;
use Procure\Application\DTO\Po\PORowDetailsDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AddRowCmdHandler extends AbstractDoctrineCmdHandler
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

        if (! $cmd->getDto() instanceof PORowDetailsDTO) {
            throw new \Exception("PORowDetailsDTO object not found!");
        }

        /**
         *
         * @var PoDTO $dto ;
         */
        $dto = $cmd->getDto();

        $notification = new Notification();

        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }

        $options = $cmd->getOptions();

        $userId = null;
        if (isset($options['userId'])) {
            $userId = $options['userId'];
        } else {
            $notification->addError("user ID not given");
        }

        /**
         *
         * @var PODoc $rootEntity ;
         */
        $rootEntity = null;
        if (isset($options['rootEntity'])) {
            $rootEntity = $options['rootEntity'];
        } else {
            $notification->addError("rootEntity not given");
        }

        if ($rootEntity == null) {
            $notification->addError("Root Entity not found");
        }

        $version = null;
        if (isset($options['version'])) {
            $version = $options['version'];
        }

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        try {

            $dto->createdBy = $userId;
            $dto->company = $rootEntity->getCompany();

            /**
             *
             * @var PORowSnapshot $snapshot ;
             */
            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new PORowSnapshot());

            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $specService = new POSpecService($sharedSpecificationFactory, $fxService);

            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);

            $cmd->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            $params = [];

            $localEntityId = $rootEntity->storeRow(__METHOD__, $params, $snapshot, $specService, $postingService);

            // event dispatch
            if (count($rootEntity->getRecordedEvents() > 0)) {

                $dispatcher = new EventDispatcher();

                foreach ($rootEntity->getRecordedEvents() as $event) {

                    $subcribers = EventHandlerFactory::createEventHandler(get_class($event), $cmd->getDoctrineEM());

                    if (count($subcribers) > 0) {
                        foreach ($subcribers as $subcriber) {
                            $dispatcher->addSubscriber($subcriber);
                        }
                    }
                    $dispatcher->dispatch(get_class($event), $event);
                }
            }

            $m = sprintf("[OK] PO Row # %s created", $localEntityId);
            $notification->addSuccess($m);
            
            $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());
            
            // revision numner has been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId())-1;
            if($version != $currentVersion){
                throw new PoVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion ));
            }
            
            $cmd->getDoctrineEM()
                ->getConnection()
                ->commit();
        } catch (\Exception $e) {

            $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();
            $cmd->getDoctrineEM()->close();
            $notification->addError($e->getMessage());
        }

        $dto->setNotification($notification);
    }
}
