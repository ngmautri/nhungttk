<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\AbstractItem;
use Inventory\Application\DTO\Item\ItemDTO;
use Application\Notification;
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
     * @var AbstractItem;
     */
    protected $item;

    /**
     *
     * @param ItemDTO $input
     */
    public function createItemFromDTO($input)
    {

        // abstract method
        $this->createItem();
        
        /**
         *
         * @var AbstractItem $item
         */
        $item = $this->item;
        
        $itemSnapshot = new ItemSnapshot();

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($itemSnapshot, $propertyName)) {

                    if ($property->getValue($input) !== null) {
                        
                        $itemSnapshot->$propertyName = $property->getValue($input);
                    }
                }
            }
        }
        
        // make from snapshot
        $item->makeItemFrom($itemSnapshot);

        /**
         * abstract methode, should return Notification object
         *
         * @var Notification $notification
         */
        $notification = $this->validate();

        if ($notification->hasErrors()) {
            throw new InvalidArgumentException($notification->errorMessage());
        }

        return $item;
    }

    /**
     * create item;
     */
    abstract function createItem();

    /**
     * validation
     */
    abstract function validate();

    /**
     * create item;
     */
    abstract function specifyItem();
    
    /**
     *
     * @param string $s
     * @return boolean
     */
    protected function isNullOrBlank($s) {
        return ($s == null || $s == "");
    }
    
}