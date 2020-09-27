<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\PostCopyFromGIOptions;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\GR\GRFromExchange;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromWhGiCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var \Inventory\Application\DTO\Transaction\TrxDTO $dto ;
         * @var GenericTrx $rootEntity ;
         * @var PostCopyFromGIOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PostCopyFromGIOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof TrxSnapshot) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "WH-GI Document required"));
        }

        try {

            $notification = new Notification();

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());

            $id = $sourceObj->getId();
            $token = $sourceObj->getToken();

            $rep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());
            $sourceObj = $rep->getRootEntityByTokenId($id, $token);

            $rootEntity = GRFromExchange::postCopyFromGI($sourceObj, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GR(Exchange) #%s copied from WH-GI #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
