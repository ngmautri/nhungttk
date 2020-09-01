<?php
namespace Procure\Application\Command\GR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\GR\Options\GrPostOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Exception\Gr\GrPostingException;
use Procure\Domain\Exception\Gr\GrVersionChangedException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrPostingValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\GLAccountValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReverseCmdHandler extends AbstractCommandHandler
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
         * @var GrDTO $dto ;
         * @var GRDoc $rootEntity ;
         * @var GRSnapshot $rootSnapshot ;
         * @var GrPostOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof GrPostOptions) {
            throw new GrPostingException("No Options given. Pls check command configuration!");
        }

        if (! $dto instanceof GrDTO) {
            throw new GrPostingException("DTO object not found!");
        }

        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();
        $trigger = $options->getTriggeredBy();

        try {

            $notification = new Notification();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $procureSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $headerValidators = new HeaderValidatorCollection();

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new GrDateAndWarehouseValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new GrPostingValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();

            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);
            $validator = new PoRowValidator($sharedSpecFactory, $fxService, $procureSpecsFactory);
            $rowValidators->add($validator);
            $validator = new GLAccountValidator($sharedSpecFactory, $fxService);
            // $rowValidators->add($validator);

            $cmdRepository = new GRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new GrPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $rootEntity->reverse($options, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("GR #%s posted", $rootEntity->getId());
            $notification->addSuccess($m);

            $queryRep = new GRQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new GrVersionChangedException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {

            $notification->addError($e->getMessage());
        }

        $dto->setNotification($notification);
    }
}
