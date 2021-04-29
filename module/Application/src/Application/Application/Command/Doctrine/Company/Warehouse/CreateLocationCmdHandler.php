<?php
namespace Application\Application\Command\Doctrine\Company\Warehouse;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocationSnapshot;
use Inventory\Infrastructure\Doctrine\WhQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateLocationCmdHandler extends AbstractCommandHandler
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

            $rep = new WHQueryRepositoryImpl($cmd->getDoctrineEM());
            $rootEntity = $rep->getById($options->getRootEntityId());

            /**
             *
             * @var BaseWarehouse $rootEntity ;
             */

            $snapshot = new BaseLocationSnapshot();
            GenericObjectAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $memberEntity = $rootEntity->createLocationFrom($snapshot, $options, $sharedService);

            $snapshot->id = $memberEntity->getId();
            $snapshot->token = $memberEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] WH Location #%s created!", $snapshot->getId());
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
