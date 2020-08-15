<?php
namespace Inventory\Application\Service\Warehouse;

use Application\Service\AbstractService;
use Inventory\Infrastructure\Doctrine\WhQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseService extends AbstractService
{

    /**
     *
     * @param int $warehouseId
     * @param string $token
     * @return NULL|\Inventory\Domain\Warehouse\GenericWarehouse
     */
    public function getWarehouse($id, $token = null)
    {
        $rep = new WhQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getByTokenId($id, $token);
    }
}
