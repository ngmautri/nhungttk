<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APReportRepositoryInterface
{

    public function getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);

    public function getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);

    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);
}
