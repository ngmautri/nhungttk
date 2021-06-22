<?php
namespace Application\Application\Command\Doctrine\Company\Brand;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateEntityCmdOptions;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\BrandSnapshotAssembler;
use Application\Domain\Company\Brand\Factory\BrandFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateBrandCmdHandler extends AbstractCommandHandler
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
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();

        try {
            $rep = new CompanyQueryRepositoryImpl($cmd->getDoctrineEM());
            $companyEntity = $rep->getById($options->getCompanyVO()
                ->getId());

            /**
             *
             * @var BaseBrand $rootEntity ;
             */
            $rootEntity = $options->getRootEntity();

            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = BrandSnapshotAssembler::updateDefaultIncludedFieldsFromArray($newSnapshot, $cmd->getData());
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
            BrandFactory::updateFrom($companyEntity, $rootEntity, $newSnapshot, $options, $params, $sharedService);

            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Brand #%s updated!", $snapshot->getId());
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
