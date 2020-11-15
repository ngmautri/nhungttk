<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
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

            $version = $options->getVersion();

            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = PORowSnapshotAssembler::updateSnapshotFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

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

            $queryRep = new POQueryRepositoryImpl($cmd->getDoctrineEM());
            // revision numner hasnt been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
            $m = sprintf("PO #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
