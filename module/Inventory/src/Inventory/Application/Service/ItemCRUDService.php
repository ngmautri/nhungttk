<?php
namespace Inventory\Application\Service;

use Application\Service\AbstractService;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Application\DTO\Item\ItemAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCRUDService extends AbstractService
{
    
    public function save($dto, $data, $userId, $isNew = FALSE, $trigger=null)
    {
        
        $dto = ItemAssembler::createItemDTOFromArray($data);
        var_dump($dto);
        
    }
}
