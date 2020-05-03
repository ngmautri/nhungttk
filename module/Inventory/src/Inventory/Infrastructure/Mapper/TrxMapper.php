<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryMv;
use Inventory\Domain\Transaction\TrxSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxMapper
{

    public static function createSnapshot(NmtInventoryMv $entity, TrxSnapshot $snapshot)
    {
        if ($entity == null)
            return null;

        if ($snapshot == null)
            return null;
    }
}
