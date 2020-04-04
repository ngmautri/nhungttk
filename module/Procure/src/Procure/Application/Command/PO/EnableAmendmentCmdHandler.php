<?php
namespace Procure\Application\Command\PO;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\PO\Options\PoAmendmentEnableOptions;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\PoAmendmentException;
use Procure\Domain\Exception\PoInvalidOperationException;
use Procure\Domain\Exception\PoVersionChangedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
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
class EnableAmendmentCmdHandler extends AbstractDoctrineCmdHandler
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
         * @var PODoc $rootEntity ;
         * @var POSnapshot $rootSnapshot ;
         * @var PoUpdateOptions $options ;
         *     
         */
        $options = $cmd->getOptions();

        if (! $options instanceof PoAmendmentEnableOptions) {
            throw new PoAmendmentException("No Options given. Pls check command configuration!");
        }

        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();

        if (! $rootEntity->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
            throw new PoInvalidOperationException(sprintf("PO is not signed and posted! %s", $rootEntity->getId()));
        }

        try {

            $cmd->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

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

            $rootEntity->enableAmendment($options, $headerValidators, $sharedService, $postingService);

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

                  
            $queryRep = new DoctrinePOQueryRepository($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new PoVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
            $cmd->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {

             $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();
             
                throw new PoAmendmentException($e->getMessage());
                
        }

     }
}
