<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PRQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getByUUID($uuid);

    public function getPRDetailsById($id, $token = null);
    
 
}
