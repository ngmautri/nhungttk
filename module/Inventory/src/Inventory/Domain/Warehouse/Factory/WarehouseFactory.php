<?php
namespace Inventory\Domain\Warehouse\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Warehouse\WhCreated;
use Inventory\Domain\Event\Warehouse\WhUpdated;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
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
     * @param ItemSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Item\GenericItem
     */
    public static function createFrom(WarehouseSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("WarehouseSnapshot not found!");
        }

        $wh = new GenericWarehouse();

        /**
         *
         * @var GenericItem $item
         */
        SnapshotAssembler::makeFromSnapshot($wh, $snapshot);

        if ($item->hasErrors()) {
            throw new \RuntimeException($item->getNotification()->errorMessage());
        }

        $item->recordedEvents = array();

        /**
         *
         * @var WarehouseSnapshot $rootSnapshot
         */
        $rootSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->store($item, true);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when creating Item #%s", $item->getId()));
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
        $item->addEvent($event);
        return $item;
    }

    /**
     *
     * @param ItemSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Item\GenericItem
     */
    public static function updateFrom(WarehouseSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if (! $snapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("ItemSnapshot not found!");
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