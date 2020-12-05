<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Notification;
use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\GR\Options\SaveCopyFromPOOptions;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Webmozart\Assert\Assert;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SaveCopyFromPOCmdHandler extends AbstractCommandHandler
{

    public function run(CommandInterface $cmd)
    {

        /**
         *
         * @var AbstractCommand $cmd ;
         * @var GenericGR $rootEntity ;
         * @var SaveCopyFromCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        // Assert::notNull($cmd->getData(), 'Input data in emty');

        Assert::isInstanceOf($cmd->getOptions(), SaveCopyFromCmdOptions::class);
        $options = $cmd->getOptions();

        $sourceObj = $options->getRootEntity();
        Assert::isInstanceOf($sourceObj, GenericAP::class);

        try {

            $snapshot = GRSnapshotAssembler::updateSnapshotFieldsFromDTO($snapshot, $dto, $editableProperties);

            $rootEntity->saveFromPO($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $m = sprintf("[OK] GR # %s saved", $dto->getId());
            $notification->addSuccess($m);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            $notification->addError($e->getMessage());
        }
    }
}
