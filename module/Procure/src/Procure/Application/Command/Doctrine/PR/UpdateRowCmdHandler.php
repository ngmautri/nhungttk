<?php
namespace Procure\Application\Command\Doctrine\PR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\PR\PRRowSnapshotModifier;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\PurchaseRequest\PRRowSnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowCmdHandler extends AbstractCommandHandler
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
         * @var PRDoc $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var PRRowSnapshot $snapshot ;
         * @var PRRowSnapshot $newSnapshot ;
         * @var PRRow $row ;
         *
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data emty!');
        Assert::isInstanceOf($cmd->getOptions(), UpdateRowCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, PRDoc::class);

        $localEntity = $options->getLocalEntity();
        Assert::isInstanceOf($localEntity, PRRow::class);

        try {

            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = PRRowSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

            $newSnapshot = PRRowSnapshotModifier::modify($newSnapshot, $cmd->getDoctrineEM(), $options->getLocale());

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

            $sharedService = SharedServiceFactory::createForPR($cmd->getDoctrineEM());
            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
            // Check Version
            // ==============
            VersionChecker::checkPRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $cmd->addSuccess(\sprintf("PO #%s updated", $rootEntity->getId()));
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
