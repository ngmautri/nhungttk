<?php
namespace Application\Application\Command\Doctrine\Company;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SaveDepartmentCmdHandler extends AbstractCommandHandler
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
        Assert::isInstanceOf($cmd, AbstractCommand::class, 'Command is not given!');
        Assert::isInstanceOf($cmd->getOptions(), CmdOptions::class, 'Cmd Option is not given!');
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();
        try {

            $snapshot = $cmd->getData()->getContextObject();
            $rep = new CompanyQueryRepositoryImpl($cmd->getDoctrineEM());
            $company = $rep->getById($options->getCompanyVO()
                ->getId());
            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $company->createDepartmentFrom($snapshot, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($company->getRecordedEvents());
            }
            // ================
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
