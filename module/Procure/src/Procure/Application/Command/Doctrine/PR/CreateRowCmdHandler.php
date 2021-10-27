<?php
namespace Procure\Application\Command\Doctrine\PR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Application\Service\PR\PRRowSnapshotModifier;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
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
         * @var PRRowSnapshot $snapshot ;
         * @var PRDoc $rootEntity ;
         *     
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateRowCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, PRDoc::class);

        try {
            $snapshot = new PRRowSnapshot();
            // PRRowSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            GenericObjectAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());

            $this->setOutput($snapshot);

            $snapshot = PrRowSnapshotModifier::modify($snapshot, $cmd->getDoctrineEM(), $options->getLocale());
            $sharedService = SharedServiceFactory::createForPR($cmd->getDoctrineEM());
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
            VersionChecker::checkPRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
