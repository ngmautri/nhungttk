<?php
namespace Procure\Application\Command\PO\Command;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CloneAndSavePOCmdHandler extends AbstractCommandHandler
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
         * @var PODoc $rootEntity ;
         * @var POSnapshot $snapshot ;
         * @var UpdateHeaderCmdOptions $options ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');
        Assert::isInstanceOf($cmd->getOptions(), UpdateHeaderCmdOptions::class);

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();

        Assert::isInstanceOf($rootEntity, PODoc::class);

        try {
            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->cloneAndSave($options, $sharedService);
            $this->setOutput($rootSnapshot);

            $m = sprintf("[OK] PO # %s copied and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
