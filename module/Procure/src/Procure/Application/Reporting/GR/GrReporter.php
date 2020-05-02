<?php
namespace Procure\Application\Reporting\GR;

use Application\Service\AbstractService;
use Procure\Application\Reporting\GR\Output\SaveAsExcel;
use Procure\Application\Reporting\GR\Output\SaveAsOpenOffice;
use Procure\Application\Reporting\GR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\Contract\HeadersSaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeadersSaveAsArray;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\GrReportRepositoryInterface;

/**
 * GR Reporter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReporter extends AbstractService
{

    /**
     *
     * @var GrReportRepositoryInterface $reporterRespository;
     */
    protected $reporterRespository;

    public function getList($filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
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
                $factory = new SaveAsExcel($builder);
                break;

            case HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

                $builder = new ExcelBuilder();
                $formatter = new DefaultHeaderFormatter();
                $factory = new SaveAsOpenOffice($builder);
                break;

            default:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getListTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("total_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getReporterRespository()->getListTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }

        return $total;
    }

    /**
     *
     * @param GrReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(GrReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\GrReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }
}
