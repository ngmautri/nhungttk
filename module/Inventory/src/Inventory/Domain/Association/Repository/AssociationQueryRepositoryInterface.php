<?php
namespace Inventory\Domain\Association\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AssociationQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);

    public function getRootEntityByTokenId($id, $token);
}
