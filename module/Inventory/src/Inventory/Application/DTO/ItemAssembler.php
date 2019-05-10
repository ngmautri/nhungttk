<?php
namespace Inventory\Application\DTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemAssembler
{

    public function createItemDTOFromArray($data)
    {
        $dto = new ItemDTO();

        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                $dto->$property = $value;
            }
        }

        return $dto;
    }
}
