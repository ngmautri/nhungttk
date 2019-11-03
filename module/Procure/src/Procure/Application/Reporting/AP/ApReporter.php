<?php
namespace Procure\Application\Reporting\AP;

use Application\Service\AbstractService;
use Procure\Application\Reporting\AP\Output\ApRowStatusInArray;
use Procure\Application\Reporting\AP\Output\ApRowStatusInExcel;
use Procure\Application\Reporting\AP\Output\ApRowStatusInOpenOffice;
use Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy;
use Procure\Infrastructure\Persistence\Doctrine\APReportRepositoryImpl;

/**
 * AP Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApReporter extends AbstractService
{

   
    private $reporterRespository;
 
    public function getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset,$outputStrategy)
    {
        $results = $this->getReporterRespository()->getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);
        if ($results == null) {
            return null;
        }

        // var_dump($results);

        $factory = null;
        switch ($outputStrategy) {
            case ApRowStatusOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new ApRowStatusInArray();
                break;
            case ApRowStatusOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new ApRowStatusInExcel();
                break;

            case ApRowStatusOutputStrategy::OUTPUT_IN_OPEN_OFFICE:
                $factory = new ApRowStatusInOpenOffice();
                break;
            default:
                $factory = new ApRowStatusInArray();
                break;
        }

        return $factory->createOutput($results);
    }

    public function getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset)
    {
        return $this->getReporterRespository()->getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);
    }
    
    

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Doctrine\APReportRepositoryImpl
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @param APReportRepositoryImpl $reporterRespository
     */
    public function setReporterRespository(APReportRepositoryImpl $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }
}
