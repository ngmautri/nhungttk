<?php
namespace Procure\Application\Command\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\PurchaseOrder\GenericPO;
use Webmozart\Assert\Assert;
use Procure\Domain\AccountPayable\Factory\APFactory;

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
         * @var APDoc $rootEntity ;
         * @var SaveCopyFromCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);

        $options = $cmd->getOptions();
        Assert::isInstanceOf($options, SaveCopyFromCmdOptions::class);

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, GenericPO::class);

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

            $snapshot = new ApDTO();
            $snapshot = APSnapshotAssembler::updateIncludedFieldsFromArray($snapshot, $cmd->getData(), $includedFields);

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            APFactory::save
            $rootSnapshot = $rootEntity->s($snapshot, $options, $sharedService);

            $snapshot->id = $rootSnapshot->getId();
            $snapshot->token = $rootSnapshot->getToken();
            $this->setOutput($snapshot);

            $m = sprintf("[OK] GR # %s copied from Quote and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
