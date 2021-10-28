<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;

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
}
