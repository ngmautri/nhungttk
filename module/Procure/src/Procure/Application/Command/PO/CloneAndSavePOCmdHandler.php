<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PODoc;

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
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not found!", get_class($cmd)));
        }

        /**
         *
         * @var PoDTO $dto ;
         * @var PoUpdateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof PoDTO) {
            throw new InvalidArgumentException("PoDTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $rootEntity = $options->getRootEntity();

        if (! $rootEntity instanceof PODoc) {
            throw new InvalidArgumentException("Root entity not found" . __METHOD__);
        }

        try {

            $notification = new Notification();
            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->cloneAndSave($options, $sharedService);

            $dto->id = $rootSnapshot->getId();
            $dto->token = $rootSnapshot->getToken();

            $m = sprintf("[OK] PO # %s copied and saved!", $rootSnapshot->getId());
            $notification->addSuccess($m);

            // event dispatcher

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
