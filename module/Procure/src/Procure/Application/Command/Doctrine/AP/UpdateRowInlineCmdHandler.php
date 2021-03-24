<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\AP\APRowSnapshotModifier;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowInlineCmdHandler extends AbstractCommandHandler
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
         * @var APDoc $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var APRowSnapshot $snapshot ;
         * @var APRowSnapshot $newSnapshot ;
         * @var APRow $row ;
         *
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data emty!');
        Assert::isInstanceOf($cmd->getOptions(), UpdateRowCmdOptions::class);
        $options = $cmd->getOptions();

        try {
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $includedFields = [
                "faRemarks",
                "remarks",
                "rowNumber",
                "docQuantity",
                "docUnit",
                "docUnitPrice",
                "conversionFactor",
                "standardConvertFactor"
            ];

            APRowSnapshotAssembler::updateIncludedFieldsFromArray($newSnapshot, $cmd->getData(), $includedFields);
            $this->setOutput($newSnapshot);

            $newSnapshot = APRowSnapshotModifier::modify($newSnapshot, $cmd->getDoctrineEM(), $options->getLocale());

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on AP #" . $rootEntity->getId());
                return;
            }

            $params = [
                "rowId" => $row->getId(),
                "rowToken" => $row->getToken(),
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $rootEntity->setLogger($cmd->getLogger());
            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            // Check Version
            // ==============
            VersionChecker::checkAPVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $m = sprintf("AP #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
