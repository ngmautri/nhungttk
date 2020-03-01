<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\Service\POSpecService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EditHeaderCmdHandler extends AbstractDoctrineCmdHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(AbstractDoctrineCmd $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof PoDTO) {
            throw new \Exception("PoDTO object not found!");
        }
        /**
         *
         * @var PoDTO $dto ;
         */
        $dto = $cmd->getDto();
        $notification = new Notification();

        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }

        $options = $cmd->getOptions();

        $rootEntityId = null;
        if (isset($options['entityId'])) {
            $rootEntityId = $options['entityId'];
        } else {
            $notification->addError("Current entityId not given");
        }

        $userId = null;
        if (isset($options['userId'])) {
            $userId = $options['userId'];
        } else {
            $notification->addError("user ID not given");
        }

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        $rootEntity = $this->getQueryRepository()->getHeaderById($rootEntityId);

        if ($rootEntity == null) {
            $notification->addError(sprintf("PO #%s can not be retrieved or empty", $rootEntityId));
            $dto->setNotification($notification);
            return;
        }

        try {

            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            /**
             *
             * @var POSnapshot $snapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = POSnapshotAssembler::updateSnapshotFromDTO($dto, $newSnapshot);
            $changeArray = $snapshot->compare($newSnapshot);
            // var_dump($changeArray);

            if ($changeArray == null) {
                $notification->addError("Nothing change on PO#" . $rootEntityId);
                $dto->setNotification($notification);
                return;
            }

            // do change
            $newSnapshot->lastChangeBy = $userId;
            $newSnapshot->lastChangeOn = new \DateTime();
            $newSnapshot->revisionNo ++;

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($this->getDoctrineEM());
            $specService = new POSpecService($sharedSpecificationFactory, $fxService);

            $newRootEntity = PODoc::updateFromSnapshot($newSnapshot, $specService);
            $this->getCmdRepository()->storeHeader($newRootEntity);

            $m = sprintf("PO #%s updated", $rootEntityId);

            $notification->addSuccess($m);
            $this->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {

            $notification->addError($e->getMessage());
            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }

        $dto->setNotification($notification);
        
    }
}
