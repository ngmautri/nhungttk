<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\PO\RowSnapshotModifier;
use function Procure\Domain\AbstractDoc\getId as sprintf;
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
class RemoveRowCmdHandler extends AbstractCommandHandler
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
        Assert::isInstanceOf($cmd->getOptions(), UpdateRowCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, PODoc::class);

        $localEntity = $options->getLocalEntity();
        Assert::isInstanceOf($localEntity, PORow::class);

        try {

            $row = $localEntity;

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootEntity->removeRow($row, $options, $sharedService);

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
            $cmd->addSuccess(\sprintf("PO row #%s removed", $row->getId()));
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
