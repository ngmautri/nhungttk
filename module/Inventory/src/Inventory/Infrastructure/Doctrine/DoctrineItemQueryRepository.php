<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemQueryRepository extends AbstractDoctrineRepository implements ItemQueryRepositoryInterface
{

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
        if ($entity == null) {
            return null;
        }

        $itemSnapshot = $this->createSnapshot($entity);

        $item = ItemFactory::createItem($itemSnapshot->itemTypeId);
        $item->makeFromSnapshot($itemSnapshot);
        return $item;
    }

    public function getByUUID($uuid)
    {}

    public function getByUUID($uuid)
    {}

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
}
