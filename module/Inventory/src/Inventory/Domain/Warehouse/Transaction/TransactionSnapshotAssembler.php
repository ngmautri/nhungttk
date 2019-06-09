<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionSnapshotAssembler
{

    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new TransactionSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
        }
    }

    /**
     *
     * @param TransactionDTO $dto
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFromDTO(TransactionDTO $dto)
    {
        if (! $dto instanceof TransactionDTO)
            return null;

        $snapShot = new TransactionSnapshot();

        $reflectionClass = new \ReflectionClass(get_class($dto));
        $properites = $reflectionClass->getProperties();

        foreach ($properites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {
                $dto->$propertyName = $property->getValue($dto);
            }
        }
        return $snapShot;
    }

    /**
     * 
     * @param array $data
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

            $snapShot = new TransactionSnapshot();

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property)) {
                $snapShot->$property = $value;
            }
        }
        return $snapShot;
    }
}
