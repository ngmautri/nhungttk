<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Procure\Application\Reporting\GR\Output\Header\HeaderSaveAsExcel;
use Procure\Application\Reporting\GR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeaderSaveAsArray;
use Procure\Application\Service\Output\Header\HeaderSaveAsSupportedType;
use Procure\Infrastructure\Contract\SqlFilterInterface;
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

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $outputStrategy = null)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getList($filter, $sort_by, $sort, $limit, $offset);

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

    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $outputStrategy = null)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeaderSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getListWithCustomDTO($filter, $sort_by, $sort, $limit, $offset);

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

    public function getListTotal(SqlFilterInterface $filter)
    {
        $total = $this->getReporterRespository()->getListTotal($filter);
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
