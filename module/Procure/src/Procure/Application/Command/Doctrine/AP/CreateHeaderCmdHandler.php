<?php
namespace Procure\Application\Command\Doctrine\AP;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\Factory\APFactory;
use Procure\Domain\Contracts\ProcureDocType;
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
             * @var APSnapshot $snapshot ;
             * @var APSnapshot $rootSnapshot ;
             * @var APDoc $rootEntity ;
             */

            $snapshot = new APSnapshot();
            APSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());

            $snapshot->setDocType(ProcureDocType::INVOICE);
            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $rootEntity = APFactory::createFrom($snapshot, $options, $sharedService);

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] AP Doc # %s created", $snapshot->getId());
            $cmd->addSuccess($m);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
