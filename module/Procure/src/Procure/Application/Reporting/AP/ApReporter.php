<?php
namespace Procure\Application\Reporting\AP;

use Application\Service\AbstractService;
use Procure\Application\Reporting\AP\Output\ApRowFormatter;
use Procure\Application\Reporting\AP\Output\SaveAsExcel;
use Procure\Application\Reporting\AP\Output\SaveAsHTML;
use Procure\Application\Reporting\AP\Output\SaveAsOpenOffice;
use Procure\Application\Reporting\AP\Output\Header\HeaderSaveAsExcel;
use Procure\Application\Reporting\AP\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Reporting\AP\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Service\Output\RowsSaveAsArray;
use Procure\Application\Service\Output\Contract\HeadersSaveAsSupportedType;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\DefaultRowFormatter;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeadersSaveAsArray;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\ApReportRepositoryInterface;

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
    public function getList($filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $results = $this->getReporterRespository()->getList($filter, $sort_by, $sort, $limit, $offset);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case HeadersSaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsArray();
                break;
            case HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeaderSaveAsExcel($builder);
                break;

            case HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

            /*
             * $builder = new PoReportOpenOfficeBuilder();
             * $formatter = new PoRowFormatter(new RowNumberFormatter());
             * $factory = new PoSaveAsOpenOffice($builder);
             * break;
             */

            default:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getListTotal(SqlFilterInterface $filter)
    {
        return $this->getReporterRespository()->getListTotal($filter);
    }

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type, $totalRecords)
    {
        $results = $this->getReporterRespository()->getAllRow($filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new ApRowFormatter(new DefaultRowFormatter());
                $factory = new RowsSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new \Procure\Application\Reporting\AP\Output\Spreadsheet\ExcelBuilder();
                $formatter = new ApRowFormatter(new RowNumberFormatter());
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new ApRowFormatter(new RowNumberFormatter());
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE:
                $formatter = new RowNumberFormatter();
                $factory = new SaveAsHTML();
                $factory->setOffset($offset);
                $factory->setLimit($limit);
                $factory->setTotalRecords($totalRecords);
                break;
            default:
                $formatter = new ApRowFormatter(new DefaultRowFormatter());
                $factory = new RowsSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return $this->getReporterRespository()->getAllRowTotal($filter);
    }

    // ==========================

    /**
     *
     * @return \Procure\Infrastructure\Persistence\ApReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @param ApReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(ApReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }
}
