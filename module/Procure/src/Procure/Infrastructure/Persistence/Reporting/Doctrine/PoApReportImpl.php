<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PoApReportInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoApReportImpl extends AbstractDoctrineRepository implements PoApReportInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}
}
