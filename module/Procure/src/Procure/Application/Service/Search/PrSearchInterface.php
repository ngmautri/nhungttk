<?php
namespace Procure\Application\Service\Search;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrSearchInterface
{

    public function search($q);

    public function searchInventoryItem($q);

    public function searchFixedAsset($q);

    public function searchServiceItem($q);

    public function createIndex();

    public function optimizeIndex();

    public function createDoc($doc, $isNew = true);

    public function updateItemIndex($itemId, $isNew = true, $optimized = false);
}
