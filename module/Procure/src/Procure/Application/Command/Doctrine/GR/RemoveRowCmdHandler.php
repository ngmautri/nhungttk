<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\GR\GRRowSnapshotModifier;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GRRowSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GenericGR;
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
         * @var GenericGR $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var GRRowSnapshot $snapshot ;
         * @var GRRowSnapshot $newSnapshot ;
         * @var GRRow $row ;
         *
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data emty!');
        Assert::isInstanceOf($cmd->getOptions(), UpdateRowCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, GenericGR::class);

        $localEntity = $options->getLocalEntity();
        Assert::isInstanceOf($localEntity, GRRow::class);

        try {

            $row = $localEntity;

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $rootEntity->removeRow($row, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
            // Check Version
            // ==============
            VersionChecker::checkGRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $cmd->addSuccess(\sprintf("GR row #%s removed", $row->getId()));
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
