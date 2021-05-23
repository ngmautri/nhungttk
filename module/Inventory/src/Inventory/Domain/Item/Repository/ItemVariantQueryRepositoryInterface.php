<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemVariantQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);

    public function getRootEntityByTokenId($id, $token);

    public function getRootEntityById($id);

    public function getItemTypeById($id);

    public function getItemSnapshotById($id, $needDetails = false);
}
