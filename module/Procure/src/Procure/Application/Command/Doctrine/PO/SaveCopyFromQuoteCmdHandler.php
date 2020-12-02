<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SaveCopyFromQuoteCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
     */
    public function run(CommandInterface $cmd)
    {

        /**
         *
         * @var PoDTO $dto ;
         * @var SaveCopyFromCmdOptions $options ;
         * @var POSnapshot $snapshot ;
         * @var PODoc $rootEntity ;
         * @var AbstractCommand $cmd ;
         *
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);

        $options = $cmd->getOptions();
        Assert::isInstanceOf($options, SaveCopyFromCmdOptions::class);

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, PODoc::class);

        try {
            // ====================

            $snapshot = $rootEntity->makeSnapshot();

            // important
            $includedFields = [
                "docCurrency",
                "docNumber",
                "docDate",
                "postingDate",
                "warehouse",
                "pmtTerm",
                "paymentMethod",
                "remarks"
            ];

            $snapshot = new PoDTO();
            $snapshot = POSnapshotAssembler::updateIncludedFieldsFromArray($snapshot, $cmd->getData(), $includedFields);

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->saveFromQuotation($snapshot, $options, $sharedService);

            $snapshot->id = $rootSnapshot->getId();
            $snapshot->token = $rootSnapshot->getToken();
            $this->setOutput($snapshot);

            $m = sprintf("[OK] PO # %s copied from Quote and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
