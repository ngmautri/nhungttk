<?php
namespace Procure\Application\Command\Doctrine\QR;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Doctrine\VersionChecker;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\QuotationRequest\QRDoc;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCmdHandler extends AbstractCommandHandler
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
         * @var QRDoc $rootEntity ;
         * @var PostCmdOptions $options ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        // Assert::notNull($cmd->getData(), 'Input data in emty');

        Assert::isInstanceOf($cmd->getOptions(), PostCmdOptions::class);
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, QRDoc::class);

        try {

            $sharedService = SharedServiceFactory::createForQR($cmd->getDoctrineEM());
            $rootEntity->post($options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() != null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Quotation #%s posted", $rootEntity->getId());
            $cmd->addSuccess($m);

            // Check Version
            // ==============
            VersionChecker::checkQRVersion($cmd->getDoctrineEM(), $rootEntity->getId(), $options->getVersion());
            // ===============
        } catch (\Exception $e) {
            $cmd->addError($e->getMessage());

            throw new \RuntimeException($e->getMessage());
        }
    }
}
