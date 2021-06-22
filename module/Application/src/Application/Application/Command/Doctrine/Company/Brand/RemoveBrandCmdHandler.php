<?php
namespace Application\Application\Command\Doctrine\Company\Brand;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\UpdateEntityCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\Factory\BrandFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
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
         * @var UpdateEntityCmdOptions $options ;
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
             * @var BaseBrand $rootEntity ;
             */
            $rootEntity = $options->getRootEntity();

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            BrandFactory::remove($companyEntity, $rootEntity, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("[OK] Brand #%s removed!", $rootEntity->getId());
            $cmd->addSuccess($m);

            // ================
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
