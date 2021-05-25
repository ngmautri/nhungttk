<?php
namespace Application\Application\Command\Doctrine\Company\ItemAttribute;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\Factory\ItemAttributeFactory;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateAttributeGroupCmdHandler extends AbstractCommandHandler
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

            $snapshot = new BaseAttributeGroupSnapshot();
            GenericObjectAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());
            $this->setOutput($snapshot); // important;

            $sharedService = SharedServiceFactory::createForCompany($cmd->getDoctrineEM());
            $rootEntity = ItemAttributeFactory::createFrom($companyEntity, $snapshot, $options, $sharedService);

            $snapshot->id = $rootEntity->getId();
            $snapshot->uuid = $rootEntity->getUuid();
            $this->setOutput($snapshot); // important;

            $m = sprintf("[OK] Item Attribute #%s created!", $snapshot->getId());
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
