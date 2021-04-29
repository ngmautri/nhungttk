<?php
namespace Application\Application\Command\Doctrine\Company\AccountChart;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\BaseChartSnapshot;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Company\AccountChart\ChartSnapshotAssembler;
use Application\Domain\Company\AccountChart\Factory\ChartFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateChartCmdHandler extends AbstractCommandHandler
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
         * @var CmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {

            $rep = new CompanyQueryRepositoryImpl($cmd->getDoctrineEM());
            $companyEntity = $rep->getById($options->getCompanyVO()
                ->getId());

            /**
             *
             * @var ChartSnapshot $snapshot ;
             * @var ChartSnapshot $rootSnapshot ;
             * @var BaseChart $rootEntity ;
             */

            $snapshot = new BaseChartSnapshot();
            ChartSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $rootEntity = ChartFactory::createFrom($companyEntity, $snapshot, $options, $sharedService);

            $snapshot->id = $rootEntity->getId();
            $snapshot->token = $rootEntity->getToken();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Account chart #%s created!", $snapshot->getId());
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
