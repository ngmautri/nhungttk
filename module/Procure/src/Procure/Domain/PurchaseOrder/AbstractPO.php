<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\GenericDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPO extends GenericDoc
{

    private function __construct()
    {}
    
    /**
     *
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public function createSnapshot($snapShot)
    {
        if ($snapShot == null) {
            return;
        }

        return SnapshotAssembler::createSnapshotFrom($this, $snapShot);
    }

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new POSnapshot());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PoDTO
     */
    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new PoDTO());
    }
}