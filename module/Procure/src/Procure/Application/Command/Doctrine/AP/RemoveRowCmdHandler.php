<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\APRowSnapshot;
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
         * @var APDoc $rootEntity ;
         * @var UpdateRowCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         * @var APRowSnapshot $snapshot ;
         * @var APRowSnapshot $newSnapshot ;
         * @var APRow $row ;
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

            $row = $localEntity;

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $rootEntity->setLogger($cmd->getLogger());
            $rootEntity->removeRow($row, $options, $sharedService);

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

            $m = sprintf("AP row #%s removed. Memory used #%s", $rootEntity->getId(), memory_get_usage());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
