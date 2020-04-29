<?php
namespace Procure\Application\Reporting\QR;

use Application\Service\AbstractService;
use Procure\Infrastructure\Persistence\ProcureReportRepositoryInterface;

/**
 * Qr Reporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ProcureReporter extends AbstractService
{

    protected $reporterRespository;

    /**
     *
     * @return \Procure\Infrastructure\Persistence\ProcureReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @param ProcureReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(ProcureReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }
}
