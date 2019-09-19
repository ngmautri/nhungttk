<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemReportRepositoryInterface
{
    public function getPriceOfItem($id, $token = null, $sort_by = null, $sort = "ASC", $limit=0, $offset=0);
}
