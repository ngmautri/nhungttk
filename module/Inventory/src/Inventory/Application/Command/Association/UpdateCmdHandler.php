<?php
namespace Inventory\Application\Command\Association;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Association\Options\UpdateOptions;
use Inventory\Application\DTO\Association\AssociationDTO;
use Inventory\Domain\Association\AssociationSnapshot;
use Inventory\Domain\Association\AssociationSnapshotAssembler;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\Validator\DefaultValidator;
use Inventory\Domain\Service\AssociationPostingService;
use Inventory\Domain\Validator\Association\AssociationValidatorCollection;
use Inventory\Infrastructure\Doctrine\AssociationItemCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\AssociationItemQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateCmdHandler extends AbstractCommandHandler
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
         * @var AssociationDTO $dto ;
         * @var UpdateOptions $options ;
         * @var BaseAssociation $rootEntity ;
         */

        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();

        if (! $options instanceof UpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        try {

            $notification = new Notification();

            /**
             *
             * @var AssociationSnapshot $snapshot ;
             * @var AssociationSnapshot $newSnapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            // not allow to change item type in this command.
            $excludedProperties = [];

            $newSnapshot = AssociationSnapshotAssembler::updateSnapshotFromDTO($newSnapshot, $dto);

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $notification->addError("Nothing change on Item #" . $rootEntityId);
                $dto->setNotification($notification);
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $cmdRepository = new AssociationItemCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new AssociationPostingService($cmdRepository);

            $validators = new AssociationValidatorCollection();

            $validator = new DefaultValidator($sharedSpecsFactory);
            $validators->add($validator);

            $newRootEntity = BaseAssociation::updateFrom($newSnapshot, $params, $options, $validators, $postingService);

            // No Check Version when Posting when posting.
            $queryRep = new AssociationItemQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! %s", $version, $currentVersion, ""));
            }

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($newRootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Item assoction updated!", $newRootEntity->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
