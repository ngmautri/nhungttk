<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
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
             * @var POSnapshot $snapshot ;
             * @var POSnapshot $rootSnapshot ;
             * @var PODoc $rootEntity ;
             */

            $snapshot = new POSnapshot();
            POSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootEntity = PODoc::createFrom($snapshot, $options, $sharedService);

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] PO # %s created", $snapshot->getId());
            $cmd->addSuccess($m);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
