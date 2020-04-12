<?php
namespace Procure\Domain\PurchaseOrder\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrQueryRepositoryInterface
{
    
    /**
     * 
     * @param int $id
     */
    public function getHeaderIdByRowId($id);
    
    /**
     * 
     * @param int $id
     * @param string $token
     */
    public function getVersion($id, $token = null);

    /**
     * 
     * @param int $id
     * @param string $token
     */
    public function getVersionArray($id, $token = null);
}
