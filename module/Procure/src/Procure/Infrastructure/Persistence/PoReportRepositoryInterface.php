<?php
namespace Procure\Infrastructure\Persistence;

use Procure\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PoReportRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getListTotal(SqlFilterInterface $filter);

    public function getOfItem($item_id, $item_token);

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getAllRowTotal(SqlFilterInterface $filter);

    public function getAllRowWithCustomDTO(SqlFilterInterface $filter);
}
