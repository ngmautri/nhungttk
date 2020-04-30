<?php
namespace Procure\Infrastructure\Persistence;

use Procure\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrReportRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getListTotal(SqlFilterInterface $filter);

    public function getOfItem($item_id, $item_token);
}
