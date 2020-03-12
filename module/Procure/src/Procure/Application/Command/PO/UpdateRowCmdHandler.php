<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\Service\POSpecService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Domain\Service\POPostingService;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowCmdHandler extends AbstractDoctrineCmdHandler
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
        
        if (! $cmd->getDto() instanceof PORowDTO) {
            throw new \Exception("PORowDTO object not found!");
        }
        
        /**
         *
         * @var PORowDTO $dto ;
         */
        $dto = $cmd->getDto();
        $notification = new Notification();
        
        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }
        
        $options = $cmd->getOptions();
        
        $rootEntityId = null;
        if (isset($options['rootEntityId'])) {
            $rootEntityId = $options['rootEntityId'];
        } else {
            $notification->addError("Current entityId not given");
        }
        
        $rootEntityToken = null;
        if (isset($options['rootEntityToken'])) {
            $rootEntityToken = $options['rootEntityToken'];
        } else {
            $notification->addError("Current entityId not given");
        }
        
        $userId = null;
        if (isset($options['userId'])) {
            $userId = $options['userId'];
        } else {
            $notification->addError("user ID not given");
        }
        
        $trigger = null;
        if (isset($options['trigger'])) {
            $trigger = $options['trigger'];
        } else {
            $notification->addError("Trigger not identifable!");
        }
        
        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }
        
        /**
         *
         * @var PODoc $rootEntity ;
         */
        $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());
        $rootEntity = $queryRep->getPODetailsById($rootEntityId,$rootEntityToken);
        
        if ($rootEntity == null) {
            $notification->addError(sprintf("PO #%s can not be retrieved or empty", $rootEntityId));
            $dto->setNotification($notification);
            return;
        }
        
        /**
         *
         * @var PORow $localEntity ;
         */
        $localEntity = $rootEntity->getRowbyTokenId($dto->getId(), $dto->getToken());
        
        if ($localEntity == null) {
            $notification->addError(sprintf("PO Row #%s can not be retrieved or empty", $dto->getId()));
            $dto->setNotification($notification);
            return;
        }
        
        try {
            
            $cmd->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit
            
            /**
             *
             * @var PORowSnapshot $snapshot ;
             */
            $snapshot = $localEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);
            
            $newSnapshot = PORowSnapshotAssembler::updateSnapshotFromDTO($dto, $newSnapshot);
            $changeLog = $snapshot->compare($newSnapshot);
            
            if ($changeLog == null) {
                $notification->addError("Nothing change on PO row#" . $rootEntityId);
                $dto->setNotification($notification);
                return;
            }
            
            $params = [
                "changeLog" => $changeLog
            ];
            
            // do change
            $newSnapshot->lastChangeBy = $userId;
            $newSnapshot->lastChangeOn = new \DateTime();
            $newSnapshot->revisionNo ++;
            
            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $specService = new POSpecService($sharedSpecificationFactory, $fxService);
            
            $newLocalEntity = PODoc::updateFromSnapshot($newSnapshot, $specService);
            
            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);
            $localEntityId = $newLocalEntity->updateHeader($trigger, $params, $specService, $postingService);
            
            // event dispatc
            if (count($newRootEntity->getRecordedEvents() > 0)) {
                
                $dispatcher = new EventDispatcher();
                
                foreach ($newRootEntity->getRecordedEvents() as $event) {
                    
                    $subcribers = EventHandlerFactory::createEventHandler(get_class($event), $cmd->getDoctrineEM());
                    
                    if (count($subcribers) > 0) {
                        foreach ($subcribers as $subcriber) {
                            $dispatcher->addSubscriber($subcriber);
                        }
                    }
                    $dispatcher->dispatch(get_class($event), $event);
                }
            }
            
            
            $m = sprintf("PO #%s updated", $rootEntityId);
            
            $notification->addSuccess($m);
            $cmd->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {
            
            $notification->addError($e->getMessage());
            $cmd->getDoctrineEM()
            ->getConnection()
            ->rollBack();
        }
        
        $dto->setNotification($notification);
    }
}
