<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Exception\InvalidArgumentException;
use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionDTOAssembler
{

    public static function createDTOFromArray($data, $doctrineEM = null)
    {
        $dto = new TransactionDTO();

        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                if ($value == null || $value == "") {
                    $dto->$property = null;
                } else {
                    $dto->$property = $value;
                }
            }
        }
        // validation.

        $notification = new Notification();
        $specFactory = new ZendSpecificationFactory();

        if (! $specFactory->getDateSpecification()->isSatisfiedBy($dto->movementDate))
            $notification->addError("Transaction date is not correct or empty");

        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->movementType)) {
            $notification->addError("Transaction Type is not correct or empty");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($dto->movementType, $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }
        }

        $specFactory1 = new DoctrineSpecificationFactory($doctrineEM);
    
        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($dto->warehouse)) {
            $notification->addError("Warehouse is empty");
        } else {
            if ($specFactory1->getWarehouseExitsSpecification()->isSatisfiedBy($dto->warehouse) == False)
                $notification->addError("Warehouse not exits...");
        }

        if ($notification->hasErrors())
            throw new InvalidArgumentException($notification->errorMessage());

        return $dto;
    }

    /**
     *
     * @param GenericTransaction $obj
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO
     */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof GenericTransaction)
            return null;

        $dto = new TransactionDTO();

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
        $entity = new \Application\Entity\NmtInventoryMv();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
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
    
    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryMv();
        $dto = new TransactionDTO();
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
}
