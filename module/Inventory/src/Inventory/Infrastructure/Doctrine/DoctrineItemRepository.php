<?php
namespace Inventory\Infrastructure\Doctrine;

use Inventory\Domain\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\ItemType;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Ramsey;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Factory\ServiceItemFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemRepository implements ItemRepositoryInterface
{

    /**
     *
     * @var EntityManager
     */
    private $doctrineEM;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        if ($em == null) {
            throw new InvalidArgumentException("Doctrine Entity manager not found!");
        }
        $this->doctrineEM = $em;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryItem")->findOneBy($criteria);
        if ($entity == null)
            return null;

        $itemSnapshot = $this->createItemSnapshot($entity);

        switch ($itemSnapshot->itemType) {

            case ItemType::INVENTORY_ITEM_TYPE:
                $factory = new InventoryItemFactory();
                break;

            case ItemType::SERVICE_ITEM_TYPE:
                $factory = new ServiceItemFactory();
                break;
            default:
                $factory = new InventoryItemFactory();
                break;
        }

        $item = $factory->createItem();
        $item->makeItemFrom($itemSnapshot);
        return $item;
        
    }

    public function getByUUID($uuid)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemRepositoryInterface::store()
     */
    public function store(AbstractItem $itemAggregate)
    {
        $entity = new \Application\Entity\NmtInventoryItem();

        $item = $itemAggregate->createItemSnapshot();

        /**
         *
         * @var ItemSnapshot $item ;
         */
        if ($item == null)
            throw new InvalidArgumentException();

        if ($item->itemGroup > 0) {
            $item_group = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->find($item->itemGroup);
            $entity->setItemGroup($item_group);
        }

        if ($item->itemCategory > 0) {
            $category = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item->itemCategory);
            $entity->setItemCategory($category);
        }

        if ($item->standardUom > 0) {
            $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $item->standardUom);
            $entity->setStandardUom($uom);
        }

        /**
         *
         * @var \Application\Entity\MlaUsers $user
         */
        $user = $this->doctrineEM->find('Application\Entity\MlaUsers', $item->createdBy);
        $entity->setCreatedBy($user);
        $entity->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());

        $entity->setCompany($user->getCompany());

        $entity->setWarehouseId($item->warehouseId); //
        $entity->setItemSku($item->itemSku);
        $entity->setItemName($item->itemName);
        $entity->setItemNameForeign($item->itemNameForeign);
        $entity->setItemDescription($item->itemDescription);
        $entity->setItemType($item->itemType);
        // $entity->setItemCategory($item->itemCategory);
        $entity->setKeywords($item->keywords);
        $entity->setIsActive($item->isActive);
        $entity->setIsStocked($item->isStocked);
        $entity->setIsSaleItem($item->isSaleItem);
        $entity->setIsPurchased($item->isPurchased);
        $entity->setIsFixedAsset($item->isFixedAsset);
        $entity->setIsSparepart($item->isSparepart);
        // $entity->setUom($item->uom);
        $entity->setBarcode($item->barcode);
        $entity->setBarcode39($item->barcode39);
        $entity->setBarcode128($item->barcode128);
        $entity->setStatus($item->status);
        $entity->setCreatedOn($item->createdOn);
        $entity->setManufacturer($item->manufacturer);
        $entity->setManufacturerCode($item->manufacturerCode);
        $entity->setManufacturerCatalog($item->manufacturerCatalog);
        $entity->setManufacturerModel($item->manufacturerModel);
        $entity->setManufacturerSerial($item->manufacturerSerial);
        $entity->setOrigin($item->origin);
        $entity->setSerialNumber($item->serialNumber);
        $entity->setLastPurchasePrice($item->lastPurchasePrice);
        $entity->setLastPurchaseCurrency($item->lastPurchaseCurrency);
        $entity->setLastPurchaseDate($item->lastPurchaseDate);
        $entity->setLeadTime($item->leadTime);
        $entity->setValidFromDate($item->validFromDate);
        $entity->setValidToDate($item->validToDate);
        $entity->setLocation($item->location);
        $entity->setItemInternalLabel($item->itemInternalLabel);
        $entity->setAssetLabel($item->assetLabel);
        $entity->setSparepartLabel($item->sparepartLabel);
        $entity->setRemarks($item->remarks);
        $entity->setLocalAvailabiliy($item->localAvailabiliy);
        // $entity->setLastChangeOn($item->lastChangeOn);
        $entity->setToken($item->token);
        $entity->setChecksum($item->checksum);
        $entity->setCurrentState($item->currentState);
        $entity->setDocNumber($item->docNumber);
        $entity->setMonitoredBy($item->monitoredBy);
        $entity->setSysNumber($item->sysNumber);
        $entity->setRemarksText($item->remarksText);
        $entity->setRevisionNo($item->revisionNo);
        $entity->setItemSku1($item->itemSku1);
        $entity->setItemSku2($item->itemSku2);
        $entity->setAssetGroup($item->assetGroup);
        $entity->setAssetClass($item->assetClass);
        $entity->setStockUomConvertFactor($item->stockUomConvertFactor);
        $entity->setPurchaseUomConvertFactor($item->purchaseUomConvertFactor);
        $entity->setSalesUomConvertFactor($item->salesUomConvertFactor);
        $entity->setCapacity($item->capacity);
        $entity->setAvgUnitPrice($item->avgUnitPrice);
        $entity->setStandardPrice($item->standardPrice);

        $entity->setItemTypeId($item->itemTypeId);

        // $entity->setItemGroup($item->itemGroup);
        // $entity->setStockUom($item->stockUom);
        // $entity->setCogsAccount($item->cogsAccount);
        // $entity->setPurchaseUom($item->purchaseUom);
        // $entity->setSalesUom($item->salesUom);
        // $entity->setInventoryAccount($item->inventoryAccount);
        // $entity->setExpenseAccount($item->expenseAccount);
        // $entity->setRevenueAccount($item->revenueAccount);
        // $entity->setDefaultWarehouse($item->defaultWarehouse);
        // $entity->setLastChangeBy($item->lastChangeBy);
        // $entity->setStandardUom($item->standardUom);
        // $entity->setCompany($item->company);
        // $entity->setLastPrRow($item->lastPrRow);
        // $entity->setLastPoRow($item->lastPoRow);
        // $entity->setLastApInvoiceRow($item->lastApInvoiceRow);
        // $entity->setLastTrxRow($item->lastTrxRow);
        // $entity->setLastPurchasing($item->lastPurchasing);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        return $entity->getId();
    }

    public function findAll()
    {}

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $entity
     *
     */
    private function createItemSnapshot($entity)
    {
        if ($entity == null)
            return null;

        $itemSnapshot = new ItemSnapshot();

        // mapping referrence
        if ($entity->getCreatedBy() !== null) {
            $itemSnapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $itemSnapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getStandardUom() !== null) {
            $itemSnapshot->standardUom = $entity->getStandardUom()->getId();
        }

        if ($entity->getCompany() !== null) {
            $itemSnapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getLastPrRow() !== null) {
            $itemSnapshot->lastPrRow = $entity->getLastPrRow()->getId();
        }

        if ($entity->getLastPoRow() !== null) {
            $itemSnapshot->lastPoRow = $entity->getLastPoRow()->getId();
        }

        if ($entity->getLastApInvoiceRow() !== null) {
            $itemSnapshot->lastApInvoiceRow = $entity->getLastApInvoiceRow()->getId();
        }

        if ($entity->getLastTrxRow() !== null) {
            $itemSnapshot->lastTrxRow = $entity->getLastTrxRow()->getId();
        }

        if ($entity->getItemGroup() !== null) {
            $itemSnapshot->itemGroup = $entity->getItemGroup()->getId();
        }

        if ($entity->getStockUom() !== null) {
            $itemSnapshot->stockUom = $entity->getStockUom()->getId();
        }

        if ($entity->getPurchaseUom() !== null) {
            $itemSnapshot->purchaseUom = $entity->getPurchaseUom()->getId();
        }

        if ($entity->getSalesUom() !== null) {
            $itemSnapshot->salesUom = $entity->getSalesUom()->getId();
        }

        if ($entity->getInventoryAccount() !== null) {
            $itemSnapshot->inventoryAccount = $entity->getInventoryAccount()->getId();
        }

        if ($entity->getExpenseAccount() !== null) {
            $itemSnapshot->expenseAccount = $entity->getExpenseAccount()->getId();
        }

        if ($entity->getRevenueAccount() !== null) {
            $itemSnapshot->revenueAccount = $entity->getRevenueAccount()->getId();
        }

        if ($entity->getDefaultWarehouse() !== null) {
            $itemSnapshot->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
        }

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($entity))) {

                if (property_exists($itemSnapshot, $propertyName)) {
                    $itemSnapshot->$propertyName = $property->getValue($entity);
                }
            }
        }

        return $itemSnapshot;
    }
}
