<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\AP\APRowSnapshotModifier;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshotAssembler;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Webmozart\Assert\Assert;
use Procure\Domain\AccountPayable\GenericAP;

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
         * @var PORowSnapshot $snapshot ;
         * @var APDoc $rootEntity ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateRowCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data is emty');

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, GenericAP::class);

        try {

            $snapshot = new APRowSnapshot();
            APRowSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot);

            $snapshot = APRowSnapshotModifier::modify($snapshot, $cmd->getDoctrineEM(), $options->getLocale());

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $localSnapshot = $rootEntity->createRowFrom($snapshot, $options, $sharedService);

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

            $m = sprintf("[OK] AP Row # %s created", $localSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
