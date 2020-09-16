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

    const GR_UN_USED = 'Unused';

    const GR_FULLY_USED = 'fully used';

    const GR_PARTIALLY_USED = 'partially used';
}