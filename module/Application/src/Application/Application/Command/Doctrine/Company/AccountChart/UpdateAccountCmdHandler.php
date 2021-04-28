<?php
namespace Application\Application\Command\Doctrine\Company\AccountChart;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\AccountChart\AccountSnapshotAssembler;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseAccountSnapshot;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Shared\Command\CommandInterface;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateAccountCmdHandler extends AbstractCommandHandler
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
         * @var UpdateMemberCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), UpdateMemberCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {

            /**
             *
             * @var BaseChart $rootEntity ;
             * @var BaseAccount $localEntity ;
             */
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $snapshot = new BaseAccountSnapshot();
            AccountSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $snapshot = $localEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = AccountSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
            $this->setOutput($newSnapshot);

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $cmd->addError("Nothing change on #" . $rootEntity->getId());
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $memberSnapshort = $rootEntity->updateAccountFrom($localEntity, $newSnapshot, $options, $sharedService);

            $snapshot->id = $memberSnapshort->getId();
            $snapshot->token = $memberSnapshort->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Account #%s updated!", $snapshot->getId());
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
