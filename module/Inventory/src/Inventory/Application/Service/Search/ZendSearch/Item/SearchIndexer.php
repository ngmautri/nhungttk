<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class SearchIndexer
{

    const INDEX_PATH = "/data/inventory/indexes/item-index";

    const FIXED_ASSET_KEY = 'isFixedAsset_key';

    const FIXED_ASSET_VALUE = 'isFixedAsset_%s';

    const STOCKED_ITEM_KEY = 'isStocked_key';

    const STOCKED_ITEM_VALUE = 'isStocked_key_%s';
}
