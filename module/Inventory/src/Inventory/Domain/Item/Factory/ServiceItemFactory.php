<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Exception\LogicException;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ServiceItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItemFromDB()
     */
    public function createItemFromDTO($input)
    {
        $spec1 = new ItemSpecification();

        $item = new ServiceItem();

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($item, $propertyName)) {
                    $item->$propertyName = $property->getValue($input);
                }
            }
        }

        // check invariants
        if (! $spec1->isSatisfiedBy($item)) {
            throw new LogicException("Can not create Service");
        }

        $item->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        $item->token = $item->uuid;
        $item->createdOn = new \DateTime();
        

        return $item;
    }
}