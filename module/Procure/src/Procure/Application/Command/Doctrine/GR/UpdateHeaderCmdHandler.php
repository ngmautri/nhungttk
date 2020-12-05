<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\PurchaseRequest\PRDoc;
use Webmozart\Assert\Assert;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateHeaderCmdHandler extends AbstractCommandHandler
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
         * @var AbstractCommand $cmd ;
         * @var GenericGR $rootEntity ;
         * @var GRSnapshot $snapshot ;
         * @var UpdateHeaderCmdOptions $options ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');
        Assert::isInstanceOf($cmd->getOptions(), UpdateHeaderCmdOptions::class);

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, GenericGR::class);

        Assert::notEq($rootEntity->getDocStatus(), ProcureDocStatus::POSTED, sprintf("GR is already posted! %s", $rootEntity->getId()));

        try {

            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = GRSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on PO#" . $rootEntity->getId());
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForPR($cmd->getDoctrineEM());
            $newRootEntity = GRFactory::updateFrom($rootEntity, $newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($newRootEntity->getRecordedEvents());
            }
            // ================

            // Check Version
            // ==============
            VersionChecker::checkGRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============
            $m = sprintf("GR #%s updated", $newRootEntity->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
