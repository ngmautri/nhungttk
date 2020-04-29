<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APListRepositoryInterface
{

    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getOfVendor($vendor_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getOfVendorTotal($vendor_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getOfItem($item_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getOfItemTotal($item_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0);

    public function getPriceOfItem($item_id, $is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);
}
