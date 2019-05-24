<?php
namespace Inventory\Application\DTO\Item;

use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\InventoryItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemAssembler
{

    public static function createItemDTOFromArray($data)
    {
        $dto = new ItemDTO();

        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                $dto->$property = $value;
            }
        }

        return $dto;
    }

    /**
     *
     * @param AbstractItem $item
     */
    public static function createItemDTO($item)
    {
        $dto = new ItemDTO();

        $reflectionClass = new \ReflectionClass($item);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $propertyName = $property->getName();
            if (property_exists($dto, $propertyName)) {
                $dto->$propertyName = $property->getValue($item);
            }
        }

        return $dto;
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $entity
     *            ;
     */
    public static function createItemDTOFromDoctrine($entity)

    {
        if (! $entity instanceof \Application\Entity\NmtInventoryItem) {

            return null;
        }

        $dto = new ItemDTO();

        // mapping referrence
        if ($entity->getCreatedBy() !== null) {
            $dto->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $dto->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getStandardUom() !== null) {
            $dto->standardUom = $entity->getStandardUom()->getId();
        }

        if ($entity->getCompany() !== null) {
            $dto->company = $entity->getCompany()->getId();
        }

        if ($entity->getLastPrRow() !== null) {
            $dto->lastPrRow = $entity->getLastPrRow()->getId();
        }

        if ($entity->getLastPoRow() !== null) {
            $dto->lastPoRow = $entity->getLastPoRow()->getId();
        }

        if ($entity->getLastApInvoiceRow() !== null) {
            $dto->lastApInvoiceRow = $entity->getLastApInvoiceRow()->getId();
        }

        if ($entity->getLastTrxRow() !== null) {
            $dto->lastTrxRow = $entity->getLastTrxRow()->getId();
        }

        if ($entity->getLastTrxRow() !== null) {
            $dto->lastTrxRow = $entity->getLastTrxRow()->getId();
        }

        if ($entity->getItemGroup() !== null) {
            $dto->itemGroup = $entity->getItemGroup()->getId();
        }

        if ($entity->getStockUom() !== null) {
            $dto->stockUom = $entity->getStockUom()->getId();
        }

        if ($entity->getPurchaseUom() !== null) {
            $dto->purchaseUom = $entity->getPurchaseUom()->getId();
        }

        if ($entity->getSalesUom() !== null) {
            $dto->salesUom = $entity->getSalesUom()->getId();
        }

        if ($entity->getInventoryAccount() !== null) {
            $dto->inventoryAccount = $entity->getInventoryAccount()->getId();
        }

        if ($entity->getExpenseAccount() !== null) {
            $dto->expenseAccount = $entity->getExpenseAccount()->getId();
        }

        if ($entity->getRevenueAccount() !== null) {
            $dto->revenueAccount = $entity->getRevenueAccount()->getId();
        }

        if ($entity->getDefaultWarehouse() !== null) {
            $dto->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
        }

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($entity))) {

                if (property_exists($dto, $propertyName)) {
                    $dto->$propertyName = $property->getValue($entity);
                }
            }
        }

        return $dto;
    }

    /**
     *
     * @return array;
     */
    public static function checkItemDTO()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryItem();
        $dto = new InventoryItem();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     * generete DTO File.
     */
    public static function createItemDTOProperities()
    {
        $entity = new \Application\Entity\NmtInventoryItem();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "private $" . $propertyName . ";";
        }
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtInventoryItem();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$item->" . $propertyName . ");";
        }
    }
}
