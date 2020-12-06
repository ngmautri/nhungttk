<?php
namespace Inventory\Application\Command\Transaction\Doctrine;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\PostCmdOptions;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\VersionChecker;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\GenericTrx;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ReverseCmdHandler extends AbstractCommandHandler
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
         * @var GenericTrx $rootEntity ;
         * @var PostCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        // Assert::notNull($cmd->getData(), 'Input data in emty');

        Assert::isInstanceOf($cmd->getOptions(), PostCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, GenericTrx::class);

        try {

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $rootEntity->reverse($options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() != null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Trx #%s reversed", $rootEntity->getId());
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
