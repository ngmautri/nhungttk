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
    
}
