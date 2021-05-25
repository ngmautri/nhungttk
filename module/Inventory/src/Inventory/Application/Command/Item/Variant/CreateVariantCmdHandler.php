<?php
namespace Inventory\Application\Command\Item\Variant;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Item\GenericItem;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateVariantCmdHandler extends AbstractCommandHandler
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
        Assert::notNull($cmd->getData(), 'Input data is emty');

        $options = $cmd->getOptions();
        $data = $cmd->getData();
        try {

            $rep = new ItemQueryRepositoryImpl($cmd->getDoctrineEM());
            $itemId = (array_shift($data));

            /**
             *
             * @var GenericItem $rootEntity
             */
            $rootEntity = $rep->getRootEntityById($itemId);
            $sharedService = SharedServiceFactory::createForItem($cmd->getDoctrineEM());
            $rootEntity->generateVariants($data, $options, $sharedService);

            $m = sprintf("[OK] Variant #%s created!", $rootEntity->getId());
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
