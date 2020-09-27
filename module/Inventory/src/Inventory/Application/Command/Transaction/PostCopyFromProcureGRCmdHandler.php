<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\CopyFromGROptions;
use Inventory\Application\Command\Transaction\Options\PostCopyFromProcureGROptions;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValidationService;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Transaction\Validator\Header\DefaultHeaderValidator;
use Inventory\Domain\Transaction\Validator\Row\DefaultRowValidator;
use Inventory\Domain\Transaction\Validator\Row\WarehouseValidator;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromProcureGRCmdHandler extends AbstractCommandHandler
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

        /**
         *
         * @var \Inventory\Application\DTO\Transaction\TrxDTO $dto ;
         * @var GRDoc $rootEntity ;
         * @var CopyFromGROptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PostCopyFromProcureGROptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof GRSnapshot) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "PO-GR Document required"));
        }

        try {

            $notification = new Notification();

            $id = $sourceObj->getId();
            $token = $sourceObj->getToken();

            $rep = new GRQueryRepositoryImpl($cmd->getDoctrineEM());
            $sourceObj = $rep->getRootEntityByTokenId($id, $token);

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());
            $rootEntity = GRFromPurchasing::postCopyFromProcureGR($sourceObj, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GR #%s copied from PO-GR #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
