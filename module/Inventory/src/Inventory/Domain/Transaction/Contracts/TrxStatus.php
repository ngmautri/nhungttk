<?php
namespace Inventory\Domain\Transaction\Contracts;

/**
 * Goods Movement Type (GM, WT)
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxStatus
{

    const UNKNOW = 'n/a';

    const GR_UN_USED = 'unused';

    const GR_FULLY_USED = 'fully used';

    const GR_PARTIALLY_USED = 'partially used';
}