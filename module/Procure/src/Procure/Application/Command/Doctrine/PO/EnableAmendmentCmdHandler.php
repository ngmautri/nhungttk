<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EnableAmendmentCmdHandler extends AbstractCommandHandler
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
        $version = $options->getVersion();

        try {

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootEntity->enableAmendment($options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $queryRep = new POQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }
        } catch (\Exception $e) {

            throw new \RuntimeException($e->getMessage());
        }
    }
}
