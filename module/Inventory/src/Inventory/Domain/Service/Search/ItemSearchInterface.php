<?php
namespace Inventory\Service\Search;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemSearchInterface
{

    public function search($q);

    public function searchInventoryItem($q);

    public function searchFixedAsset($q);

    public function searchServiceItem($q);

    public function createIndex();

    public function optimizeIndex();

    public function createDoc($doc);
}
