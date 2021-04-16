<?php
namespace Application\Domain\Company\Contracts\Account;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AssetType
{

    const CASH_BANK = 'bs_asset_1';

    const RECEIVEABLE = 'bs_asset_2';

    const PREPAYMENT = 'bs_asset_3';

    const INVENTORY = 'bs_asset_4';

    const SUPPLIES = 'bs_asset_5';

    const OTHER_CURRENT_ASSET = 'bs_asset_6';

    const NONE_CURRENT_ASSET = 'bs_asset_7';

    const FIXED_ASSET = 'bs_asset_8';

    const OTHER_FIXED_ASSET = 'bs_asset_9';
}
