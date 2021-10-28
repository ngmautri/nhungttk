<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\AccountPayable\Factory\APFactory;
use Webmozart\Assert\Assert;

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
         * @var APDoc $rootEntity ;
         * @var APSnapshot $snapshot ;
         * @var UpdateHeaderCmdOptions $options ;
         *     
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');
        Assert::isInstanceOf($cmd->getOptions(), UpdateHeaderCmdOptions::class);

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, GenericAP::class);

        try {

            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = APSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on PO#" . $rootEntity->getId());
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $newRootEntity = APFactory::updateFrom($rootEntity, $newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($newRootEntity->getRecordedEvents());
            }
            // ================

            // Check Version
            // ==============
            VersionChecker::checkAPVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============

            $m = sprintf('AP #%s updated', $newRootEntity->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
