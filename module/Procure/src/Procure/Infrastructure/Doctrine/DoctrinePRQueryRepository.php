<?php
namespace Procure\Infrastructure\Doctrine;

use Procure\Domain\PurchaseRequest\PRQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRQueryRepository implements PRQueryRepositoryInterface
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
