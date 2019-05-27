<?php
namespace Inventory\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemListRepositoryInterface
{

    public function getItems($item_type = null, $is_active = null, $is_fixed_asset = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);
    public function getTotalItem($item_type = null, $is_active = null, $is_fixed_asset = null);
    
}
