<?php
namespace Application\Application\Command\Doctrine\Company\AccountChart;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\AccountChart\AccountSnapshotAssembler;
use Application\Domain\Company\AccountChart\BaseAccountSnapshot;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateAccountCmdHandler extends AbstractCommandHandler
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
         * @var CreateMemberCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateMemberCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {

            $rep = new ChartQueryRepositoryImpl($cmd->getDoctrineEM());
            $rootEntity = $rep->getById($options->getRootEntityId());

            /**
             *
             * @var ChartSnapshot $snapshot ;
             * @var ChartSnapshot $rootSnapshot ;
             * @var BaseChart $rootEntity ;
             */

            $snapshot = new BaseAccountSnapshot();
            AccountSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $memberEntity = $rootEntity->createAccountFrom($snapshot, $options, $sharedService);

            $snapshot->id = $memberEntity->getId();
            $snapshot->token = $memberEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Account #%s created!", $snapshot->getId());
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
