<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PrHeaderHelper;
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface::getOfItem()
     */
    public function getOfItem(SqlFilterInterface $filterHeader)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows)
    {
        $results = PrHeaderHelper::getPRList($this->getDoctrineEM(), $filterHeader, $filterRows);
        return PrHeaderHelper::createGenerator($this->getDoctrineEM(), $results);
    }

    public function getListTotal(SqlFilterInterface $filterHeader, SqlFilterInterface $filterRows)
    {
        return PrHeaderHelper::getPRListTotal($this->getDoctrineEM(), $filterHeader, $filterRows);
    }

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