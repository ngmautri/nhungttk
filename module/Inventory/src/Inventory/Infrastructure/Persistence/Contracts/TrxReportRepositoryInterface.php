<?php
namespace Inventory\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TrxReportRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getListTotal(SqlFilterInterface $filter);

    public function getOfItem($item_id, $item_token);

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getAllRowIssueFor(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getAllRowTotal(SqlFilterInterface $filter);

    public function getBeginGrGiEnd(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);
}
