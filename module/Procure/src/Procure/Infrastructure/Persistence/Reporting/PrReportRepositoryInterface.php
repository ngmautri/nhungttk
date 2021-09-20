<?php
namespace Procure\Infrastructure\Persistence\Reporting;

use Procure\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrReportRepositoryInterface
{

    public function getList(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows);

    public function getListTotal(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows);

    public function getOfItem($item_id, $item_token);

    public function getAllRow(SqlFilterInterface $filter);

    public function getAllRowTotal(SqlFilterInterface $filter);
}
