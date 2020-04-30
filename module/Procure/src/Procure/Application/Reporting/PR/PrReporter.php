<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Application\Reporting\GR\Output\Header\HeaderSaveAsExcel;
use Procure\Application\Reporting\GR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeaderSaveAsArray;
use Procure\Application\Service\Output\Header\HeaderSaveAsSupportedType;
use Procure\Infrastructure\Persistence\PrReportRepositoryInterface;

/**
 * GR Reporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReporter extends AbstractService
{

    /**
     *
     * @var PrReportRepositoryInterface $reporterRespository;
     */
    protected $reporterRespository;

    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        if ($outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        // var_dump($results);

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

    public function getListWithCustomDTO($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0, $outputStrategy = null)
    {
        if ($outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getListWithCustomDTO($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset, new PrHeaderDetailDTO());

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
        $total = $this->getReporterRespository()->getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
        return $total;
    }

    /**
     *
     * @param PrReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(PrReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\PrReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }
}
