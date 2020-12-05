<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GRRowSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GenericGR;
use Webmozart\Assert\Assert;
use Procure\Application\Service\GR\GRRowSnapshotModifier;

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
         * @var GRRowSnapshot $snapshot ;
         * @var GenericGR $rootEntity ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateRowCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, GenericGR::class);

        try {
            $snapshot = new GRRowSnapshot();
            GRRowSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot);

            $snapshot = GRRowSnapshotModifier::modify($snapshot, $cmd->getDoctrineEM(), $options->getLocale());
            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("[OK] PR Row # %s created", $localSnapshot->getId());
            $cmd->addSuccess($m);

            // Check Version
            // ==============
            VersionChecker::checkGRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
