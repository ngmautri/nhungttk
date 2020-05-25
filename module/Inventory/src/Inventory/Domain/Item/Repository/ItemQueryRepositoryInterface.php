<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function findAll();

    public function getById($id);

    public function getByUUID($uuid);

    public function generateSysNumber($obj);
}
