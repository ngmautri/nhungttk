<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\ApReportRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApReportRepositoryImpl extends AbstractDoctrineRepository implements ApReportRepositoryInterface
{

    public function getOfItem($item_id, $item_token)
    {}

    public function getAllRowTotal(SqlFilterInterface $filter)
    {}

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}

    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}

    public function getListTotal(SqlFilterInterface $filter)
    {}

    public function getAllRowWithCustomDTO(SqlFilterInterface $filter)
    {}

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}
}
