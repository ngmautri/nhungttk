<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoAmendmentEnableOptions;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\PoAmendmentException;
use Procure\Domain\Exception\PoInvalidOperationException;
use Procure\Domain\Exception\PoVersionChangedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\Validator\DefaultHeaderValidator;
use Procure\Domain\PurchaseOrder\Validator\HeaderValidatorCollection;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Domain\PurchaseOrder\Validator\RowValidatorCollection;
use Procure\Domain\PurchaseOrder\Validator\DefaultRowValidator;
use Procure\Application\Command\PO\Options\PoAmendmentAcceptOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AcceptAmendmentCmdHandler extends AbstractDoctrineCmdHandler
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

        if (! $cmd->getDto() instanceof PoDTO) {
            throw new \Exception("PoDTO object not found!");
        }

        /**
         *
         * @var PoDTO $dto ;
         * @var PODoc $rootEntity ;
         * @var POSnapshot $rootSnapshot ;
         * @var PoAmendmentAcceptOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PoAmendmentAcceptOptions) {
            throw new PoAmendmentException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof PoDTO) {
            throw new PoAmendmentException("PoDTO object not found!");
        }

        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();
        $trigger = $options->getTriggeredBy();

        try {

            $cmd->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            $notification = new Notification();

            /**
             *
             * @var POSnapshot $snapshot ;
             * @var POSnapshot $rootSnapshot ;
             * @var PODoc $rootEntity ;
             */

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new DoctrinePOCmdRepository($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $rootEntity->acceptAmmendment($options, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatc
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

            $m = sprintf("Amendment of P0O #%s posted!", $rootEntity->getId());
            $notification->addSuccess($m);

            $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new PoVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
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
