<?php
namespace Inventory\Application\Service;

use Application\Service\AbstractService;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Application\DTO\ItemAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCRUDService extends AbstractService
{
    
    public function save($dto, $data, $userId, $isNew = FALSE, $trigger=null)
    {
        
        if (! $userId == null) {
            $m = $this->controllerPlugin->translate("Invalid Argument! User is not identified for this transaction.");
            $errors[] = $m;
        }
        
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            $m = $this->controllerPlugin->translate("Invalid Argument. AP Object not found!");
            $errors[] = $m;
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        $newDTO = ItemAssembler::createItemDTOFromArray($data);
        
        
    }
}
