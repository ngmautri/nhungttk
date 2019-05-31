<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Application\Notification;
use Inventory\Domain\Exception\InvalidArgumentException;
use Doctrine\Entity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowDTOAssembler
{

    /**
     *
     * @param array $data
     * @param Entity $doctrineEM
     * @throws InvalidArgumentException
     * @return \Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO
     */
    public static function createDTOFromArray($data, $doctrineEM = null)
    {
        $dto = new TransactionRowDTO();

        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                if ($value == null || $value == "") {
                    $dto->$property = null;
                } else {
                    $dto->$property = $value;
                }
            }
        }

        // Pre-Validation Only.

        $notification = new Notification();
        $specFactory = new ZendSpecificationFactory();

        // check if assigned to transaction.
        
        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->movement)) {
            $notification->addError("Transaction is not found!");
        } else {
            if (! $specFactory->getPositiveNumberSpecification()->isSatisfiedBy($dto->movement))
                $notification->addError("Quantity is not valid!");
        }
        
        
        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->docQuantity)) {
            $notification->addError("Quantity is not given!");
        } else {
            if (! $specFactory->getPositiveNumberSpecification()->isSatisfiedBy($dto->docQuantity))
                $notification->addError("Quantity is not valid!");
        }

        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->docQuantity)) {
            $notification->addError("Quantity is not given!");
        } else {
            if (! $specFactory->getPositiveNumberSpecification()->isSatisfiedBy($dto->docQuantity))
                $notification->addError("Quantity is not valid!");
        }

        $specFactory1 = new DoctrineSpecificationFactory($doctrineEM);

        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->item)) {
            $notification->addError("Item is not selected");
        } else {
            if ($specFactory1->getItemExitsSpecification()->isSatisfiedBy($dto->item) == False)
                $notification->addError(sprintf("Item #%s not exits...", $dto->item));
        }
     
        if ($notification->hasErrors())
            throw new InvalidArgumentException($notification->errorMessage());

        return $dto;
    }

    /**
     *
     * @param TransactionRow $obj
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO
     */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof TransactionRow)
            return null;

        $dto = new TransactionRowDTO();

        $reflectionClass = new \ReflectionClass($obj);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (property_exists($dto, $propertyName)) {
                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $dto->$propertyName = null;
                } else {
                    $dto->$propertyName = $property->getValue($obj);
                }
            }
        }

        return $dto;
    }

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        $entity = new \Application\Entity\NmtInventoryTrx();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    /**
     * generete DTO File.
     */
    public static function createAutoGereatedFields()
    {
        $dto = new TransactionDTO();
        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();
        $auto_generated = array();
        foreach ($itemProperites as $property) {
            // @system_genereted
            if (preg_match('/@system_genereted/', $property->getDocComment()) == 1) {
                $auto_generated[] = $property->getName();
            }
        }
        return $auto_generated;
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtInventoryMv();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$snapshot->" . $propertyName . ");";
        }
    }
}
