<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Helper\PrGrReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrGrReportImpl extends AbstractDoctrineRepository implements PrGrReportInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface::getList()
     */
    public function getList(ProcureAppSqlFilterInterface $filter)
    {
        $result = PrGrReportHelper::getList($this->getDoctrineEM(), $filter);
        return new ArrayCollection($result);
    }

    public function getListTotal(ProcureAppSqlFilterInterface $filter)
    {
        return PrGrReportHelper::getListTotal($this->getDoctrineEM(), $filter);
    }
}
