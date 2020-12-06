<?php
namespace Inventory\Application\Command\Transaction\Doctrine;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CreateHeaderCmdOptions;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\TrxSnapshotAssembler;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Webmozart\Assert\Assert;

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
             * @var TrxSnapshot $snapshot ;
             * @var TrxSnapshot $rootSnapshot ;
             * @var GenericTrx $rootEntity ;
             */

            $snapshot = new TrxSnapshot();
            TrxSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM(), $cmd->getLogger());
            $rootEntity = TransactionFactory::createFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] WH Trx  # %s created", $snapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
