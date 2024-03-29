<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface POListRepositoryInterface
{

    public function getPoOfItem($item_id, $token);

    public function getPoList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getPOStatus($id, $token);

    public function getPoRowOfVendor($vendor_id = null, $vendor_token = null, $sort_by = null, $order = 'DESC', $limit = 0, $offset = 0);

    public function getOpenPoGr($id, $token);

    public function getOpenPoAP($id, $token);

    public function getAllPoRowStatus($is_active = 1, $po_year, $balance = 1, $sort_by, $sort, $limit, $offset);

    public function getAllPoRowStatusTotal($is_active = 1, $po_year, $balance = 1);
}
