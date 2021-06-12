<?php
namespace Inventory\Domain\Item\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Item\ItemCreated;
use Inventory\Domain\Event\Item\ItemUpdated;
use Inventory\Domain\Item\FixedAssetItem;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\GenericItemSnapshot;
use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Inventory\Domain\Item\NoneInventoryItem;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\Validator\ValidatorFactory;
use Inventory\Domain\Service\SharedService;
use Webmozart\Assert\Assert;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemFactory
{

    public static function contructFromDB($snapshot)
    {
        if (! $snapshot instanceof GenericItemSnapshot) {
            throw new InvalidArgumentException("ItemSnapshot not found!");
        }

        $itemTypeId = $snapshot->getItemTypeId();
        $instance = self::_createItem($itemTypeId);
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->specifyItem(); // important
        $instance->createUom();

        return $instance;
    }

    /**
     *
     * @deprecated
     * @param int $itemTypeId
     * @return \Inventory\Domain\Item\InventoryItem
     */
    public static function createItem($itemTypeId)
    {
        switch ($itemTypeId) {

            case ItemType::INVENTORY_ITEM_TYPE:
                $item = new InventoryItem();
                break;

            case ItemType::NONE_INVENTORY_ITEM_TYPE:
                $item = new NoneInventoryItem();
                break;

            case ItemType::SERVICE_ITEM_TYPE:
                $item = new ServiceItem();
                break;

            case ItemType::FIXED_ASSET_ITEM_TYPE:
                $item = new FixedAssetItem();
                break;
            default:
                $item = new InventoryItem();
                break;
        }

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
    public static function createFrom(ItemSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "ItemSnapshot not found");
        Assert::notNull($sharedService, "SharedService service not found");

        $itemTypeId = $snapshot->getItemTypeId();
        $item = self::_createItem($itemTypeId);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericItem $item
         */
        GenericObjectAssembler::updateAllFieldsFrom($item, $snapshot);

        $item->specifyItem(); // important
        $item->createUom();

        $validators = ValidatorFactory::create($itemTypeId, $sharedService);
        $item->validate($validators);

        if ($item->hasErrors()) {
            throw new \RuntimeException($item->getNotification()->errorMessage());
        }

        $item->clearEvents();

        /**
         *
         * @var ItemSnapshot $rootSnapshot
         * @var ItemCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($item, true);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ItemCreated($target, $defaultParams, $params);
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
    public static function updateFrom(GenericItem $rootEntity, ItemSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($rootEntity, "GenericItem not found.");
        Assert::notNull($snapshot, "ItemSnapshot not found!");
        Assert::notNull($options, "Cmd options not found!");
        Assert::notNull($options, "SharedService service not found!");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericItem $item
         */
        ItemSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);
        $rootEntity->specifyItem();

        $validators = ValidatorFactory::create($rootEntity->getItemTypeId(), $sharedService);
        $rootEntity->validate($validators);

        if ($rootEntity->hasErrors()) {
            throw new \RuntimeException($rootEntity->getNotification()->errorMessage());
        }

        $rootEntity->clearEvents();

        /**
         *
         * @var ItemSnapshot $rootSnapshot
         */
        $rootSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->store($rootEntity, false);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ItemUpdated($target, $defaultParams, $params);
        $rootEntity->addEvent($event);

        return $rootEntity;
    }

    private static function _createItem($itemTypeId)
    {
        $item = null;

        if (! \in_array($itemTypeId, ItemType::getSupportedType())) {
            throw new InvalidArgumentException("Item type empty or not supported! #" . $itemTypeId);
        }

        switch ($itemTypeId) {

            case ItemType::INVENTORY_ITEM_TYPE:
                $item = new InventoryItem();
                break;

            case ItemType::NONE_INVENTORY_ITEM_TYPE:
                $item = new NoneInventoryItem();
                break;

            case ItemType::SERVICE_ITEM_TYPE:
                $item = new ServiceItem();
                break;

            case ItemType::FIXED_ASSET_ITEM_TYPE:
                $item = new FixedAssetItem();
                break;
            default:
                $item = new InventoryItem();
                break;
        }
        Assert::notNull($item, 'Can not create item!');
        return $item;
    }
}