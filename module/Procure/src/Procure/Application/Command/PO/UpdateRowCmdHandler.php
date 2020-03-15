<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof PORowDetailsDTO) {
            throw new \Exception("PORowDetailsDTO object not found!");
        }

        /**
         *
         * @var PORowDetailsDTO $dto ;
         */
        $dto = $cmd->getDto();
        $notification = new Notification();

        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }

        $options = $cmd->getOptions();

        /**
         *
         * @var PODoc $rootEntity ;
         */
        $rootEntity = null;
        if (isset($options['rootEntity'])) {
            $rootEntity = $options['rootEntity'];
        } else {
            $notification->addError("RootEntiy not given");
        }

        $localEntity = null;
        if (isset($options['localEntity'])) {
            $localEntity = $options['localEntity'];
        } else {
            $notification->addError("LocalEntity not given");
        }

        $entityId = null;
        if (isset($options['entityId'])) {
            $entityId = $options['entityId'];
        } else {
            $notification->addError("entityId not given");
        }

        $entityToken = null;
        if (isset($options['entityToken'])) {
            $entityToken = $options['entityToken'];
        } else {
            $notification->addError("entityToken not given");
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

        if ($rootEntity == null) {
            $notification->addError("PO #%s can not be retrieved or empty");
        }

        if ($localEntity == null) {
            $notification->addError("PO Row #%s can not be retrieved or empty");
        }

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        /**
         *
         * @var PORow $row ;
         */
        $row = $localEntity;

        if ($row == null) {
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
             * @var PORowSnapshot $newSnapshot ;
             *     
             */
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "isActive",
                "remarks",
                "rowNumber",
                "item",
                "prRow",
                "vendorItemCode",
                "vendorItemName",
                "docQuantity",
                "docUnit",
                "docUnitPrice",
                "conversionFactor",
                "descriptionText",
                "taxRate"
            ];

            /*
             * $newSnapshot->rowNumber;
             */

            $newSnapshot = PORowSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);
            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $notification->addError("Nothing change on PO row#" . $row->getId());
                $dto->setNotification($notification);
                return;
            }

            $params = [
                "rowId" => $row->getId(),
                "rowToken" => $row->getToken(),
                "changeLog" => $changeLog
            ];

            // do change
            $newSnapshot->lastchangeBy = $userId;
            $newSnapshot->revisionNo ++;

            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $specService = new POSpecService($sharedSpecificationFactory, $fxService);

            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);

            $rootEntity->updateRowFromSnapshot($trigger, $params, $row, $newSnapshot, $specService, $postingService);

            // event dispatcher
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
            
            
            $m = sprintf("PO #%s updated. Event Count #%s", $rootEntity->getId(),   count($rootEntity->getRecordedEvents()) );
            
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
