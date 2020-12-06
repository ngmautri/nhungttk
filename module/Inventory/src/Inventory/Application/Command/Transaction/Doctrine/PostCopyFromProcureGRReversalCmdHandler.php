<?php
namespace Inventory\Application\Command\Transaction\Doctrine;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\PostCopyFromCmdOptions;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Procure\Domain\GoodsReceipt\GenericGR;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromProcureGRReversalCmdHandler extends AbstractCommandHandler
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
         * @var GenericGR $sourceObj ;
         * @var PostCopyFromCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        // Assert::notNull($cmd->getData(), 'Input data in emty');

        Assert::isInstanceOf($cmd->getOptions(), PostCopyFromCmdOptions::class);
        $options = $cmd->getOptions();

        $sourceObj = $options->getRootEntity();
        Assert::isInstanceOf($sourceObj, GenericGR::class);

        try {

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $sharedService->setLogger($cmd->getLogger());
            $rootEntity = TransactionFactory::postCopyFromProcureGRReversal($sourceObj, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() != null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GR #%s copied from PO-GR #%s and posted!", $rootEntity->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
