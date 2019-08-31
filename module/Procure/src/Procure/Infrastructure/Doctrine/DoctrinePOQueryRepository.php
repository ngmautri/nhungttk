<?php
namespace Procure\Infrastructure\Doctrine;

use Procure\Domain\PurchaseOrder\POQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRQueryRepository implements POQueryRepositoryInterface
{

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}
