<?php
namespace Inventory\Application\Command\Transaction\Doctrine;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CreateRowCmdOptions;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\VersionChecker;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Application\Service\Transaction\TrxRowSnapshotModifier;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\TrxRowSnapshotAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        /**
         *
         * @var CreateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var CreateRowCmdOptions $options ;
         * @var TrxRowSnapshot $snapshot ;
         * @var GenericTrx $rootEntity ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateRowCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, GenericTrx::class);

        try {
            $snapshot = new TrxRowSnapshot();
            TrxRowSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot);

            $snapshot = TrxRowSnapshotModifier::modify($snapshot, $cmd->getDoctrineEM(), $options->getLocale());
            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());
            $sharedService->setLogger($cmd->getLogger());

            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("[OK] Trx Row # %s created", $localSnapshot->getId());
            $cmd->addSuccess($m);

            // Check Version
            // ==============
            VersionChecker::checkTrxVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
