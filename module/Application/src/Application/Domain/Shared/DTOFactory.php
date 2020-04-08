<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DTOFactory
{

    /**
     *
     * @param object $dto
     * @param array $data
     * @return object
     */
    public static function createDTOFromArray($data, $dto)
    {
        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                if ($value == null || $value == "") {
                    $dto->$property = null;
                } else {
                    $dto->$property = $value;
                }
            }
        }
        return $dto;
    }

    /**
     *
     * @param object $obj
     * @param object $dto
     * @return NULL|object
     */
    public static function createDTOFrom($obj, $dto)
    {
        if ($obj == null or $dto == null) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($obj);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
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
}
