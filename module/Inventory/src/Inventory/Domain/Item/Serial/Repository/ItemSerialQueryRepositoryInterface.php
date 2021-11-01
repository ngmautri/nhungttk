<?php
namespace Inventory\Domain\Item\Serial\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemSerialQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);

    public function getByTokenId($id, $token);

    public function getOfItem($itemId);

    public function getOfInvoice($invoiceId);

    public function getOfPOGoodReceipt($grId);

    public function getList(ItemSerialSqlFilter $filter);

    public function getListTotal(ItemSerialSqlFilter $filter);
}