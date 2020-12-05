<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\PostCopyFromCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromAPByWarehouseCmdHandler extends AbstractCommandHandler
{

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
            $sharedService->setLogger($cmd->getLogger());

            $docs = GRFactory::postCopyFromAPByWarehouse($sourceObj, $options, $sharedService);

            foreach ($docs as $rootEntity) {

                // event dispatch
                // ================
                if ($cmd->getEventBus() !== null) {
                    $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
                }
                // ================
            }

            $m = sprintf("GR #%s copied from AP #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
