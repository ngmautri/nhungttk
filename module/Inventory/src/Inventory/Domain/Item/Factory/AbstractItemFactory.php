<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\AbstractItem;
use Inventory\Application\DTO\Item\ItemDTO;

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
        $item = $this->item;

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($item, $propertyName)) {

                    if ($property->getValue($input) !== null) {
                        $item->$propertyName = $property->getValue($input);
                    }
                }
            }
        }
        
        // abstract methode
        $this->specifyItem();

        // abstract methode
        $this->validate();

                
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
    
}