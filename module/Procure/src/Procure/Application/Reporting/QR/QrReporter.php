<?php
namespace Procure\Application\Reporting\QR;

use Application\Service\AbstractService;
use Procure\Infrastructure\Persistence\QrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;

/**
 * Qr Reporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrReporter extends AbstractService
{

    /**
     *
     * @var QrReportRepositoryImpl $reporterRespository;
     */
    protected $reporterRespository;

    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        $results = $this->getReporterRespository()->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
        return $results;
    }

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        return $this->getReporterRespository()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
    }

    /**
     *
     * @param QrReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(QrReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\QrReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }
}
