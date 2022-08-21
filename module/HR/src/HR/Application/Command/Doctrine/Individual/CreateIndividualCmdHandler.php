<?php
namespace HR\Application\Command\Doctrine\Individual;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use HR\Application\Command\Options\CreateIndividualCmdOptions;
use HR\Application\Service\SharedServiceFactory;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Employee\BaseIndividualSnapshot;
use HR\Domain\Employee\IndividualSnapshotAssembler;
use HR\Domain\Employee\Factory\IndividualFactory;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateIndividualCmdHandler extends AbstractCommandHandler
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
         * @var CreateIndividualCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CreateIndividualCmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {

            /**
             *
             * @var BaseIndividualSnapshot $snapshot ;
             * @var BaseIndividualSnapshot $rootSnapshot ;
             * @var BaseIndividual $rootEntity ;
             */

            $snapshot = new BaseIndividualSnapshot();
            IndividualSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForIndividual($cmd->getDoctrineEM());
            $rootEntity = IndividualFactory::createFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Individuals # %s created", $snapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
