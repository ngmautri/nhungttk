<?php
namespace Application\Application\Command\Doctrine\Company\Brand;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RemoveBrandCmdHandler extends AbstractCommandHandler
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
             * @var BaseWarehouse $rootEntity ;
             * @var BaseLocation $localEntity ;
             */
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $rootEntity->removeLocation($localEntity, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("[OK] WH Location #%s removed!", $localEntity->getId());
            $cmd->addSuccess($m);

            // ================
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
