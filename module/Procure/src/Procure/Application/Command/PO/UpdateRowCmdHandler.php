<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoRowUpdateOptions;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\PoRowUpdateException;
use Procure\Domain\Exception\PoVersionChangedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Domain\PurchaseOrder\Validator\DefaultHeaderValidator;
use Procure\Domain\PurchaseOrder\Validator\DefaultRowValidator;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
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
            throw new PoRowUpdateException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }
        /**
         *
         * @var PORowDetailsDTO $dto ;
         * @var PODoc $rootEntity ;
         * @var PoRowUpdateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $options instanceof PoRowUpdateOptions) {
            throw new PoRowUpdateException("No Options given. Pls check command configuration!");
        }
        if (! $cmd->getDto() instanceof PORowDetailsDTO) {
            throw new PoRowUpdateException("PORowDetailsDTO object not found!");
        }

        try {
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $userId = $options->getUserId();
            $version = $options->getVersion();

            $notification = new Notification();

            /**
             *
             * @var PORowSnapshot $snapshot ;
             * @var PORowSnapshot $newSnapshot ;
             * @var PORow $row ;
             *     
             */
            $row = $localEntity;
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

            $cmd->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            $newSnapshot->lastchangeBy = $userId;
            $newSnapshot->revisionNo ++;
            
            $headerValidators = new HeaderValidatorCollection();
            
            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            
            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);
            
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);
            
            
            $cmdRepository = new POCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);            

            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $headerValidators, $rowValidators, $sharedService, $postingService);
            
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

            $m = sprintf("PO #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());

            $notification->addSuccess($m);

            $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());            
            // revision numner hasnt been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId())-1;
            if($version != $currentVersion){
                throw new PoVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion ));
            }
            
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
