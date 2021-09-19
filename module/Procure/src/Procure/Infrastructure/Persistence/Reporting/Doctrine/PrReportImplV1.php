<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrRowHelper;
use Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReportImplV1 extends AbstractDoctrineRepository implements PrReportRepositoryInterface
{

    public function getOfItem($itemId, $itemToken)
    {}

    public function getList(SqlFilterInterface $filter)
    {}

    public function getListTotal(SqlFilterInterface $filter)
    {}

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return PrRowHelper::getTotalRows($this->getDoctrineEM(), $filter);
    }

    public function getAllRow(SqlFilterInterface $filter)
    {
        $rows = PrRowHelper::getRows($this->getDoctrineEM(), $filter);
        return PrRowHelper::createRowsGenerator($this->getDoctrineEM(), $rows);
    }
}