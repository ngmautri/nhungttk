<?php
namespace Inventory\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemTrxReportRepositoryInterface
{

    public function getInOutOnhand(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getInOutOnhandTotal(SqlFilterInterface $filter);
}
