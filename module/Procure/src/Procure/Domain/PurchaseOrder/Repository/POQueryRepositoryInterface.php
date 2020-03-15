<?php
namespace Procure\Domain\PurchaseOrder\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface POQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);
    
    public function getHeaderDTO($id, $token = null);

    public function getByUUID($uuid);

    public function getPODetailsById($id, $token = null);
}
