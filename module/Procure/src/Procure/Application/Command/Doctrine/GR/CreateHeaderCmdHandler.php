<?php
namespace Procure\Application\Command\Doctrine\GR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\PurchaseRequest\PRDoc;
use Webmozart\Assert\Assert;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateHeaderCmdHandler extends AbstractCommandHandler
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
         * @var CreateHeaderCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateHeaderCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {

            /**
             *
             * @var GRSnapshot $snapshot ;
             * @var GRSnapshot $rootSnapshot ;
             * @var GRDoc $rootEntity ;
             */

            $snapshot = new GRSnapshot();
            GRSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $rootEntity = GRFactory::createFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Good movement # %s created", $snapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
