<?php
namespace Inventory\Domain\Association;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Association\Repository\AssociationItemCmdRepositoryInterface;
use Inventory\Domain\Event\Association\AssociationCreated;
use Inventory\Domain\Event\Association\AssociationUpdated;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Exception\ValidationFailedException;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Inventory\Domain\Validator\Association\AssociationValidatorCollection;
use Inventory\Infrastructure\Persistence\Contracts\AssociationCmdRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseAssociation extends AbstractAssociation
{

    public static function contructFromDB($snapshot)
    {
        if (! $snapshot instanceof AssociationSnapshot) {
            throw new InvalidArgumentException("AssociationSnapshot not found!");
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param AssociationSnapshot $snapshot
     * @param CommandOptions $options
     * @param AssociationValidatorCollection $validators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Inventory\Domain\Association\BaseAssociation
     */
    public static function createFrom(AssociationSnapshot $snapshot, CommandOptions $options, AssociationValidatorCollection $validators, PostingServiceInterface $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $validators, $postingService);

        $cmdRep = $postingService->getCmdRepository();
        if (! $cmdRep instanceof AssociationItemCmdRepositoryInterface) {
            throw new InvalidArgumentException("AssociationCmdRepositoryInterface is given");
        }
        if ($options == null) {
            throw new InvalidArgumentException("Options is null");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->clearNotification();
        $instance->validate($validators);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var AssociationSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $cmdRep->store($instance, true);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when creating item association #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();
        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getUuid());
        $defaultParams->setTargetDocVersion($rootSnapshot->getVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new AssociationCreated($target, $defaultParams, $params);
        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param AssociationSnapshot $snapshot
     * @param array $params
     * @param CommandOptions $options
     * @param AssociationValidatorCollection $validators
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Association\BaseAssociation
     */
    public static function updateFrom(AssociationSnapshot $snapshot, $params, CommandOptions $options, AssociationValidatorCollection $validators, PostingServiceInterface $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $validators, $postingService);

        $cmdRep = $postingService->getCmdRepository();
        if (! $cmdRep instanceof AssociationCmdRepositoryInterface) {
            throw new InvalidArgumentException("AssociationCmdRepositoryInterface is given");
        }
        if ($options == null) {
            throw new InvalidArgumentException("Options is null");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->clearNotification();
        $instance->validate($validators);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var AssociationSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $cmdRep->store($instance, false);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when creating item association #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();
        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getUuid());
        $defaultParams->setTargetDocVersion($rootSnapshot->getVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new AssociationUpdated($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param AssociationValidatorCollection $validator
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     */
    public function validate(AssociationValidatorCollection $validator, $isPosting = false)
    {
        if (! $validator instanceof AssociationValidatorCollection) {
            throw new InvalidArgumentException("Validators not given!");
        }

        $validator->validate($this);
    }

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new AssociationSnapshot());
    }

    /**
     *
     * @param AssociationSnapshot $snapshot
     * @param AssociationValidatorCollection $validators
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     */
    private function _checkInputParams(AssociationSnapshot $snapshot, AssociationValidatorCollection $validators, PostingServiceInterface $postingService)
    {
        if (! $snapshot instanceof AssociationSnapshot) {
            throw new InvalidArgumentException("TrxSnapshot not found!");
        }

        if ($validators == null) {
            throw new InvalidArgumentException("AssociationValidatorCollection not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Association\AbstractAssociation";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }
}