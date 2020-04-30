<?php
namespace Procure\Application\Reporting\AP;

use Application\Service\AbstractService;
use Procure\Application\Reporting\AP\Output\ApRowStatusInArray;
use Procure\Application\Reporting\AP\Output\ApRowStatusInExcel;
use Procure\Application\Reporting\AP\Output\ApRowStatusInOpenOffice;
use Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy;
use Procure\Application\Reporting\AP\Output\Header\HeaderSaveAsExcel;
use Procure\Application\Reporting\AP\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeaderSaveAsArray;
use Procure\Application\Service\Output\Header\HeaderSaveAsSupportedType;
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

    // Header
    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        if ($outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $results = $this->getReporterRespository()->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case HeaderSaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeaderSaveAsArray();
                break;
            case HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeaderSaveAsExcel($builder);
                break;

            case HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

            /*
             * $builder = new PoReportOpenOfficeBuilder();
             * $formatter = new PoRowFormatter(new RowNumberFormatter());
             * $factory = new PoSaveAsOpenOffice($builder);
             * break;
             */

            default:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeaderSaveAsArray();
                break;
        }

        return $factory->saveMultiplyHeaderAs($results, $formatter);
    }

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        return $this->getReporterRespository()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
    }

    public function getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset, $outputStrategy)
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

    // ==========================

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
