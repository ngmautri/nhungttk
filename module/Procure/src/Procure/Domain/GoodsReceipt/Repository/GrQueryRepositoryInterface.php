<?php
namespace Procure\Domain\GoodsReceipt\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface GrQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getHeaderDTO($id, $token = null);

    public function getByUUID($uuid);

    public function getPODetailsById($id, $token = null);

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);
}
