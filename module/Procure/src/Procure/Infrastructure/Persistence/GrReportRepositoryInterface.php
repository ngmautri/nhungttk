<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface GrReportRepositoryInterface
{

    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0);

    public function getOfItem($item_id, $item_token);
}
