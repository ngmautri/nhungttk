<?php
namespace Procure\Application\Command\GR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromAPCmdHandler extends AbstractCommandHandler
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
         * @var GrDTO $dto ;
         * @var GRDoc $rootEntity ;
         * @var PostCopyFromAPOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PostCopyFromAPOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof GenericAP) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "AP Document required"));
        }

        try {

            $notification = new Notification();

            $sharedService = SharedServiceFactory::createForGR($cmd->getDoctrineEM());
            $rootEntity = GRFactory::postCopyFromAP($sourceObj, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("GR #%s copied from AP #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);

            // logging
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
