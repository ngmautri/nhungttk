<?php
namespace Application\Application\Command\Doctrine\Company\Warehouse;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateEntityCmdOptions;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LockWarehouseCmdHandler extends AbstractCommandHandler
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
        Assert::isInstanceOf($cmd->getOptions(), UpdateEntityCmdOptions::class);

        $options = $cmd->getOptions();

        try {
            $rep = new CompanyQueryRepositoryImpl($cmd->getDoctrineEM());
            $companyEntity = $rep->getById($options->getCompanyVO()
                ->getId());

            /**
             *
             * @var BaseWarehouse $rootEntity ;
             * @var BaseLocation $localEntity ;
             */
            $rootEntity = $options->getRootEntity();
            $snapshot = $rootEntity->makeSnapshot();

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $rootEntity->lockWarehouse($companyEntity, $options, $sharedService);
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] WH #%s locked!", $snapshot->getId());
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
