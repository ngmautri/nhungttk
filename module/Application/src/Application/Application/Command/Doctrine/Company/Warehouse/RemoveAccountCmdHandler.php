<?php
namespace Application\Application\Command\Doctrine\Company\AccountChart;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Shared\Command\CommandInterface;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RemoveAccountCmdHandler extends AbstractCommandHandler
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

        $options = $cmd->getOptions();

        try {

            /**
             *
             * @var BaseChart $rootEntity ;
             * @var BaseAccount $localEntity ;
             */
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $rootEntity->removeAccount($localEntity, $options, $sharedService);

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
