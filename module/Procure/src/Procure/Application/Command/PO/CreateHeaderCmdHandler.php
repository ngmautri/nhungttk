<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Application\Application\Command\AbstractDoctrineCmd;
use Procure\Domain\Service\POPostingService;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateHeaderCmdHandler extends AbstractDoctrineCmdHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", get_class($cmd)));
        }

        if ($cmd->getDto() == null) {
            throw new \Exception("DTO object not found!");
        }

        /**
         *
         * @var PoDTO $dto ;
         */
        $dto = $cmd->getDto();

        if (! $dto instanceof PoDTO) {
            throw new \Exception("PoDTO object not found!");
        }

        $notification = new Notification();

        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }

        $options = $cmd->getOptions();

        $companyId = null;
        if (isset($options['companyId'])) {
            $companyId = $options['companyId'];
        } else {
            $notification->addError("Company ID not given");
        }

        $userId = null;
        if (isset($options['userId'])) {
            $userId = $options['userId'];
        } else {
            $notification->addError("user ID not given");
        }

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        $companyQueryRepository = new DoctrineCompanyQueryRepository($cmd->getDoctrineEM());
        $company = $companyQueryRepository->getById($companyId);

        if ($company == null) {
            $notification->addError("Company not found");
            $dto->setNotification($notification);
            return;
        }

        $dto->company = $companyId;
        $dto->createdBy = $userId;
        $dto->docStatus = PODocStatus::DOC_STATUS_DRAFT;
        $dto->currency = $dto->getDocCurrency();

        /**
         *
         * @var POSnapshot $snapshot ;
         */
        $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new POSnapshot());
        $snapshot->localCurrency = $company->getDefaultCurrency();

        $entityRoot = PODoc::makeFromSnapshot($snapshot);

        $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
        $fxService = new FXService();
        $fxService->setDoctrineEM($cmd->getDoctrineEM());
        $specService = new POSpecService($sharedSpecFactory, $fxService);

        $cmd->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);
            $rootEntityId = $entityRoot->storeHeader($specService, $postingService);

            // $rootEntityId = $rep->storeHeader($entityRoot);

            $m = sprintf("[OK] PO # %s created", $rootEntityId);
            $notification->addSuccess($m);

            // event dispatc
            if (count($entityRoot->getRecordedEvents() > 0)) {

                $dispatcher = new EventDispatcher();

                foreach ($entityRoot->getRecordedEvents() as $event) {

                    $subcribers = EventHandlerFactory::createEventHandler(get_class($event),$cmd->getDoctrineEM());

                    if (count($subcribers) > 0) {
                        foreach ($subcribers as $subcriber) {
                            $dispatcher->addSubscriber($subcriber);
                        }
                    }
                    $dispatcher->dispatch(get_class($event), $event);
                }
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
