<?php
namespace Procure\Application\Reporting\QR;

use Application\Service\AbstractService;
use Procure\Application\Reporting\QR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\Contract\HeadersSaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeadersSaveAsArray;
use Procure\Application\Service\QR\Output\SaveAsExcel;
use Procure\Infrastructure\Contract\SqlFilterInterface;
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

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getList($filter, $sort_by, $sort, $limit, $offset);

        // var_dump($results);

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
                $factory = new SaveAsExcel($builder);
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
