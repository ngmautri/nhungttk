<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class SearchIndexer
{

    const INDEX_PATH = "/data/inventory/indexes/item_idx";

    const ITEM_SERIAL_INDEX_PATH = "/data/inventory/indexes/item_serial_idx";

    const FIXED_ASSET_KEY = 'isFixedAsset_key';

    const FIXED_ASSET_VALUE = 'isFixedAsset_%s';

    const STOCKED_ITEM_KEY = 'isStocked_key';

    const STOCKED_ITEM_VALUE = 'isStocked_key_%s';

    const ITEM_SERIAL_KEY = '_item_serial_key';

    const ITEM_SERIAL_FORMAT = '_%_%';

    const ITEM_KEY = '_item_key';

    const ITEM_KEY_FORMAT = '_item_%s';

    public static function getItemKeyQuery($id)
    {
        $k = self::ITEM_KEY;
        $v = \sprintf(self::ITEM_KEY_FORMAT, $id);
        $ck_query = \sprintf('%s:%s', $k, $v);
        return $ck_query;
    }
}
