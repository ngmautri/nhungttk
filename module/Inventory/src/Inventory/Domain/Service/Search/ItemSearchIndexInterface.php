<?php
namespace Inventory\Domain\Service\Search;

use Inventory\Domain\Item\ItemSnapshot;

/**
 * Item Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemSearchIndexInterface
{

    public function createDoc(ItemSnapshot $doc);

    public function optimizeIndex();

    public function createIndex($rows);
}
