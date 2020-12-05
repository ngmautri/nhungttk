<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use PHPUnit\Framework\Assert;
use Procure\Application\Command\Options\PostCopyFromCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromAPReservalByWarehouseCmdHandler extends AbstractCommandHandler
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
         * @var PostCopyFromCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        // Assert::notNull($cmd->getData(), 'Input data in emty');

        Assert::isInstanceOf($cmd->getOptions(), PostCopyFromCmdOptions::class);
        $options = $cmd->getOptions();

        $sourceObj = $options->getRootEntity();
        Assert::isInstanceOf($sourceObj, GenericAP::class);
        try {

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());

            $docs = GRFactory::postCopyFromAPReversalByWarehouse($sourceObj, $options, $sharedService);

            foreach ($docs as $rootEntity) {

                // event dispatch
                // ================
                if ($cmd->getEventBus() !== null) {
                    $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
                }
                // ================
            }

            $m = sprintf("GR Reversal #%s copied from AP Reveral #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $cmd->addSuccess($m);

            // logging
        } catch (\Exception $e) {

            $cmd->addError($$e->getMessage());
            throw $e;
        }
    }
}
