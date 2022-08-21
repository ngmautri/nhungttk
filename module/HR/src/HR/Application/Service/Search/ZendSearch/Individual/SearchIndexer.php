<?php
namespace HR\Application\Service\Search\ZendSearch\Individual;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class SearchIndexer
{

    const INDEX_PATH = "/data/hr/indexes/individual_idx";

    const INDIVIDUAL_KEY = '_individual_key';

    const INDIVIDUAL_KEY_VALUE_FORMAT = '_individual_%s';

    // ======================================
    const ITEM_SERIAL_INDEX_PATH = "/data/hr/indexes/item_serial_idx";

    const FIXED_ASSET_KEY = 'isFixedAsset_key';

    const FIXED_ASSET_VALUE = 'isFixedAsset_%s';

    const STOCKED_ITEM_KEY = 'isStocked_key';

    const STOCKED_ITEM_VALUE = 'isStocked_key_%s';

    const ITEM_SERIAL_KEY = '_item_serial_key';

    const ITEM_SERIAL_FORMAT = '_%_%';

    const ITEM_KEY = '_item_key';

    const ITEM_KEY_FORMAT = '_item_%s';

    const VARIANT_KEY = 'isVariant_key';

    const SERIAL_KEY = 'isSerial_key';

    const VARIANT_VALUE = 'variant_%';

    const SERIAL_VALUE = 'sn_%';

    const YES = 'yes';

    const NO = 'no';

    public static function getItemKeyQuery($id)
    {
        $k = self::ITEM_KEY;
        $v = \sprintf(self::ITEM_KEY_FORMAT, $id);
        $ck_query = \sprintf('%s:%s', $k, $v);
        return $ck_query;
    }
}
