<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\AccountChart\Repository\ChartQueryRepositoryInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartQueryRepositoryImpl extends AbstractDoctrineRepository implements ChartQueryRepositoryInterface
{

    public function getById($id, CompanySqlFilterInterface $filter)
    {}

    public function getByUUID($uuid, CompanySqlFilterInterface $filter)
    {}
}
