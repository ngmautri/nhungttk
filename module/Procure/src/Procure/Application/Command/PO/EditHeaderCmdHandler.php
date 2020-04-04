<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\PoInvalidOperationException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Exception\PoVersionChangedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\PurchaseOrder\Validator\DefaultHeaderValidator;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EditHeaderCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not foundsv!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof PoDTO) {
            throw new \Exception("PoDTO object not found!");
        }

        /**
         *
         * @var PoDTO $dto ;
         * @var PODoc $rootEntity ;
         * @var POSnapshot $rootSnapshot ;
         * @var PoUpdateOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PoUpdateOptions) {
            throw new PoUpdateException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof PoDTO) {
            throw new PoUpdateException("PoDTO object not found!");
        }

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();

        try {

            $notification = new Notification();

            if ($rootEntity->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
                throw new PoInvalidOperationException(sprintf("PO is already posted! %s", $rootEntity->getId()));
            }

            /**
             *
             * @var POSnapshot $snapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = POSnapshotAssembler::updateSnapshotFromDTO($dto, $newSnapshot);
            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {

                // No Notify when posting.
                if (! $options->getIsPosting()) {
                    $notification->addError("Nothing change on PO#" . $rootEntityId);
                    $dto->setNotification($notification);
                }
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            // do change
            $newSnapshot->lastChangeBy = $userId;

            /**
             *
             * @var POSnapshot $snapshot ;
             * @var POSnapshot $rootSnapshot ;
             * @var PODoc $rootEntity ;
             */
            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $newRootEntity = PODoc::updateFrom($newSnapshot, $options, $params, $headerValidators, $sharedService, $postingService);

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

            $m = sprintf("PO #%s updated", $newRootEntity->getId());

            $notification->addSuccess($m);

            // No Check Version when Posting when posting.
            $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new PoVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {

            $notification->addError($e->getMessage());
        }

        $dto->setNotification($notification);
    }
}
