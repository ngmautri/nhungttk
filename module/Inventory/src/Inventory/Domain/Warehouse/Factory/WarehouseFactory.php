<?php
namespace Inventory\Domain\Warehouse\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Warehouse\WhCreated;
use Inventory\Domain\Event\Warehouse\WhUpdated;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\Contracts\DefaultLocation;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Validator\ValidatorFactory;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseFactory
{

    public static function contructFromDB($snapshot)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("WarehouseSnapshot not found!");
        }

        $instance = new GenericWarehouse();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param WarehouseSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Warehouse\GenericWarehouse
     */
    public static function createFrom(WarehouseSnapshot $snapshot, CommandOptions $options = null, SharedService $sharedService = null)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("WarehouseSnapshot not found!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options is empty");
        }

        $createdDate = new \DateTime();
        $userId = $options->getUserId();
        $snapshot->init($userId, date_format($createdDate, 'Y-m-d H:i:s'));

        $wh = new GenericWarehouse();
        SnapshotAssembler::makeFromSnapshot($wh, $snapshot);

        // root location
        $rootUUID = Uuid::uuid4()->toString();

        $location = new GenericLocation();
        $location->setUuid($rootUUID);
        $location->setToken($location->getToken());
        $location->setIsActive(1);
        $location->setLocationName(DefaultLocation::ROOT_LOCATION);
        $location->setLocationCode(DefaultLocation::ROOT_LOCATION);
        $location->setIsRootLocation(1);
        $location->setIsSystemLocation(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $wh->addLocation($location);

        // recycle location
        $location = new GenericLocation();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setParentUuid($rootUUID);
        $location->setToken($location->getToken());

        $location->setLocationName(DefaultLocation::RECYCLE_LOCATION);
        $location->setLocationCode(DefaultLocation::RECYCLE_LOCATION);
        $location->setIsReturnLocation(1);
        $location->setIsSystemLocation(1);
        $location->setIsActive(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $location->setUuid(Uuid::uuid4()->toString());
        $wh->addLocation($location);

        // recycle location
        $location = new GenericLocation();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setParentUuid($rootUUID);
        $location->setToken($location->getToken());

        $location->setLocationName(DefaultLocation::RETURN_LOCATION);
        $location->setLocationCode(DefaultLocation::RETURN_LOCATION);
        $location->setIsReturnLocation(1);
        $location->setIsSystemLocation(1);
        $location->setIsActive(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $location->setUuid(Uuid::uuid4()->toString());
        $wh->addLocation($location);

        // scrap location
        $location = new GenericLocation();
        $location->setUuid(Uuid::uuid4()->toString());
        $location->setParentUuid($rootUUID);
        $location->setToken($location->getToken());

        $location->setLocationName(DefaultLocation::SCRAP_LOCATION);
        $location->setLocationCode(DefaultLocation::SCRAP_LOCATION);
        $location->setIsScrapLocation(1);
        $location->setIsSystemLocation(1);
        $location->setIsActive(1);
        $location->setCreatedBy($userId);
        $location->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $wh->addLocation($location);

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::CREATE_NEW_WH);

        // create default location.
        $wh->validate($validationService);

        if ($wh->hasErrors()) {
            throw new \RuntimeException($wh->getNotification()->errorMessage());
        }

        $wh->clearEvents();

        /**
         *
         * @var WarehouseSnapshot $rootSnapshot ;
         * @var WhCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($wh, true);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating Warehouse #%s", $wh->getId()));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhCreated($target, $defaultParams, $params);
        $wh->addEvent($event);
        return $wh;
    }

    public static function updateFrom(WarehouseSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("WarehouseSnapshot not found!");
        }

        $wh = new GenericWarehouse();
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericItem $item
         */
        SnapshotAssembler::makeFromSnapshot($wh, $snapshot);

        $validationService = ValidatorFactory::create($sharedService, ValidatorFactory::EDIT_WH);
        $wh->validate($validationService);

        if ($wh->hasErrors()) {
            throw new \RuntimeException($item->getNotification()->errorMessage());
        }

        $item->recordedEvents = array();

        /**
         *
         * @var WarehouseSnapshot $rootSnapshot
         */
        $rootSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->store($item, false);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when creating Warehouse #%s", $item->getId()));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new WhUpdated($target, $defaultParams, $params);
        $item->addEvent($event);

        return $item;
    }
}