<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APFromPO;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Webmozart\Assert\Assert;

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
         * @var APFromPO $rootEntity ;
         * @var SaveCopyFromCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);

        $options = $cmd->getOptions();
        Assert::isInstanceOf($options, SaveCopyFromCmdOptions::class);

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, APFromPO::class);

        try {
            // ====================

            $snapshot = $rootEntity->makeSnapshot();

            // important
            $includedFields = [
                "docCurrency",
                "docNumber",
                "docDate",
                "postingDate",
                "grDate",
                "warehouse",
                "pmtTerm",
                "paymentMethod",
                "remarks"
            ];

            $snapshot = new ApDTO();
            $snapshot = APSnapshotAssembler::updateIncludedFieldsFromArray($snapshot, $cmd->getData(), $includedFields);

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->saveFromPO($snapshot, $options, $sharedService);

            $snapshot->id = $rootSnapshot->getId();
            $snapshot->token = $rootSnapshot->getToken();
            $this->setOutput($snapshot);

            $m = sprintf("[OK] AP # %s copied from PO and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
