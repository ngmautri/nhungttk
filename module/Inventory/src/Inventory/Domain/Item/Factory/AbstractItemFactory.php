<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractItemFactory
{

    /**
     *
     * @var GenericItem;
     */
    protected $item;

    /**
     *
     * @param \Inventory\Application\DTO\Item\ItemDTO $input
     */
    public function createItemFromDTO($input)
    {

        /**
         * Abstract Method
         *
         * @var GenericItem $item
         */
        $item = $this->createItem();

        $itemSnapshot = new ItemSnapshot();

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($itemSnapshot, $propertyName)) {

                    if ($property->getValue($input) == null || $property->getValue($input)=="") {
                        $itemSnapshot->$propertyName = null;                        
                    } else {
                        $itemSnapshot->$propertyName = $property->getValue($input);
                    }
                }
            }
        }

        // make from snapshot
        $item->makeItemFrom($itemSnapshot);

        /**
         * Abstract Method
         */
        $this->specifyItem();

        $notification = $item->validate();

        if ($notification->hasErrors())
            throw new InvalidArgumentException($notification->errorMessage());

        return $item;
    }

    /**
     *
     * @param ItemSnapshot $itemSnapshot
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Item\GenericItem
     */
    public function createItemFromSnapshot($itemSnapshot)
    {
        if ($itemSnapshot == null)
            throw new InvalidArgumentException("Item Snapshot is empty");

        /**
         * Abstract Method
         *
         * @var GenericItem $item
         */
        $item = $this->createItem();

        // make from snapshot
        $item->makeItemFrom($itemSnapshot);
        /**
         * Abstract Method
         */
        $this->specifyItem();

        $notification = $item->validate();

        if ($notification->hasErrors())
            throw new InvalidArgumentException($notification->errorMessage());

        return $item;
    }

    /**
     * create item;
     */
    abstract function createItem();

    /**
     * create item;
     */
    abstract function specifyItem();
    
   
}