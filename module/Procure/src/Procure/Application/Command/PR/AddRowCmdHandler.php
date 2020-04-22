<?php
namespace Procure\Application\Command\PR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PR\Options\RowCreateOptions;
use Procure\Application\DTO\Pr\PrRowDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Application\Service\PR\RowSnapshotReference;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\PurchaseRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\PurchaseRequest\Validator\Row\DefaultRowValidator;
use Procure\Domain\Service\PRPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\PRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AddRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof PrRowDTO) {
            throw new InvalidArgumentException("PrRowDTO object not found!");
        }

        if (! $cmd->getOptions() instanceof RowCreateOptions) {
            throw new InvalidArgumentException("Cmd Options object not found!");
        }

        /**
         *
         * @var PrRowDTO $dto ;
         * @var InvalidArgumentException $options ;
         * @var PRRowSnapshot $snapshot ;
         * @var PRDoc $rootEntity ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        $notification = new Notification();

        $userId = $options->getUserId();
        $rootEntity = $options->getRootEntity();
        $version = $options->getVersion();

        try {

            $dto->createdBy = $userId;
            $dto->company = $rootEntity->getCompany();

            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new PRRowSnapshot());
            $snapshot = RowSnapshotReference::updateReferrence($snapshot, $cmd->getDoctrineEM());

            $sharedSpecificationFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $sharedService = new SharedService($sharedSpecificationFactory, $fxService);

            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecificationFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecificationFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new PRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new PRPostingService($cmdRepository);

            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

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

            $m = sprintf("[OK] Row # %s created", $localSnapshot->getId());
            $notification->addSuccess($m);

            $queryRep = new PRQueryRepositoryImpl($cmd->getDoctrineEM());

            $dto->setNotification($notification);

            // revision numner has been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
