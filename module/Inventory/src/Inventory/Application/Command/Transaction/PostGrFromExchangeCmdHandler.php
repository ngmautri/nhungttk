<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\PostGRFromExchangeOptions;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\GR\GRFromExchange;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostGrFromExchangeCmdHandler extends AbstractCommandHandler
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
         * @var TrxDoc $rootEntity ;
         * @var PostGRFromExchangeOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PostGRFromExchangeOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        /**
         *
         * @var TrxSnapshot $sourceObj ;
         */
        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof TrxSnapshot) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "WH-GI Document required"));
        }

        try {

            $notification = new Notification();
            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());
            $sharedService->setLogger($cmd->getLogger());

            $id = $sourceObj->getId();
            $token = $sourceObj->getToken();

            $rep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());
            $sourceObject = $rep->getRootEntityByTokenId($id, $token);

            $rootEntity = GRFromExchange::postCopyFromGI($sourceObject, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GR #%s copied from WH-GE #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
