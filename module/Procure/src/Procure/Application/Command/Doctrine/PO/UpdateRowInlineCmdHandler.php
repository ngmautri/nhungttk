<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\PO\PORowSnapshotModifier;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
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
         * @var PODoc $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var PORowSnapshot $snapshot ;
         * @var PORowSnapshot $newSnapshot ;
         * @var PORow $row ;
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

            $incluedFields = [
                "faRemarks",
                "remarks",
                "rowNumber",
                "docQuantity",
                "docUnit",
                "docUnitPrice",
                "conversionFactor"
            ];

            $newSnapshot = PORowSnapshotAssembler::updateIncludedFieldsFromArray($newSnapshot, $cmd->getData(), $incluedFields);
            $this->setOutput($newSnapshot);
            $newSnapshot = PORowSnapshotModifier::modify($newSnapshot, $cmd->getDoctrineEM(), $options->getLocale());

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on PO#" . $rootEntity->getId());
                return;
            }

            $params = [
                "rowId" => $row->getId(),
                "rowToken" => $row->getToken(),
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            // Check Version
            // ==============
            VersionChecker::checkPOVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $m = sprintf("PO #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
