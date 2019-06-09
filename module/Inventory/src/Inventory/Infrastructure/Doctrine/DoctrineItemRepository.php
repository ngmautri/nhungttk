<?php
namespace Inventory\Infrastructure\Doctrine;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Factory\AbstractItemFactory;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Ramsey;
use Inventory\Domain\Item\Factory\ItemFactory;

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

        $itemSnapshot = $this->createSnapshot($entity);

        $item = ItemFactory::createItem($itemSnapshot->itemTypeId);
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
    public function store(GenericItem $itemAggregate, $generateSysNumber = True)
    {
        if ($itemAggregate == null)
            throw new InvalidArgumentException("Item is empty");

        // create snapshot
        $item = $itemAggregate->createSnapshot();

        /**
         *
         * @var ItemSnapshot $item ;
         */
        if ($item == null)
            throw new InvalidArgumentException("ItemSnapshot can be created");

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         */
        if ($itemAggregate->getId() > 0) {
            $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryItem", $itemAggregate->getId());

            if ($entity == null) {
                throw new InvalidArgumentException("Item cant not retrived.");
            }

            $entity->setLastChangeOn($item->lastChangeOn);
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $entity = new \Application\Entity\NmtInventoryItem();
            $entity->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());
            $entity->setToken($entity->getUuid());

            if ($generateSysNumber == True) {
                $entity->setSysNumber($this->generateSysNumber($entity));
            }

            if ($item->createdBy > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $u ;
                 */
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $item->createdBy);
                if ($u !== null) {
                    $entity->setCreatedBy($u);
                    $entity->setCompany($u->getCompany());
                }
            }

            $entity->setCreatedOn(new \DateTime());
        }

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

        if ($item->stockUom > 0) {
            $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $item->stockUom);
            $entity->setStockUom($uom);
        }

        if ($item->purchaseUom > 0) {
            $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $item->purchaseUom);
            $entity->setPurchaseUom($uom);
        }

        if ($item->salesUom > 0) {
            $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $item->salesUom);
            $entity->setSalesUom($uom);
        }

        if ($item->lastChangeBy > 0) {
            $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $item->lastChangeBy);
            $entity->setLastChangeBy($u);
        }

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

        // $entity->setToken($item->token);
        // $entity->setChecksum($item->checksum);
        $entity->setCurrentState($item->currentState);
        $entity->setDocNumber($item->docNumber);
        $entity->setMonitoredBy($item->monitoredBy);

        // $entity->setSysNumber($item->sysNumber);

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
    private function createSnapshot($entity)
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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemRepositoryInterface::generateSysNumber()
     */
    public function generateSysNumber($obj)
    {
        $criteria = array(
            'isActive' => 1,
            'subjectClass' => get_class($obj)
        );

        /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
        $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);

        if ($docNumber instanceof \Application\Entity\NmtApplicationDocNumber) {

            $maxLen = strlen($docNumber->getToNumber());
            $currentLen = 1;
            $currentDoc = $docNumber->getPrefix();
            $current_no = $docNumber->getCurrentNumber();

            if ($current_no == null) {
                $current_no = $docNumber->getFromNumber();
            } else {
                $current_no ++;
                $currentLen = strlen($current_no);
            }

            $docNumber->setCurrentNumber($current_no);
            
            $tmp = "";
            for ($i = 0; $i < $maxLen - $currentLen; $i ++) {
                
                $tmp = $tmp . "0";
            }
            
            $currentDoc = $currentDoc . $tmp . $current_no;
            return $currentDoc;
        }
        
        return null;
    }

}
