<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APDocQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getByUUID($uuid);
}
