<?php
namespace Procure\Application\Command\Doctrine\PR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CloneAndSaveCmdHandler extends AbstractCommandHandler
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
         * @var AbstractCommand $cmd ;
         * @var PRDoc $rootEntity ;
         * @var PRSnapshot $snapshot ;
         * @var UpdateHeaderCmdOptions $options ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), UpdateHeaderCmdOptions::class);

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, PRDoc::class);

        try {
            $sharedService = SharedServiceFactory::createForPR($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->cloneAndSave($options, $sharedService);
            $this->setOutput($rootSnapshot);

            $m = sprintf("[OK] PR # %s copied and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw $e;
        }
    }
}
