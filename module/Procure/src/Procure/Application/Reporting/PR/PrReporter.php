<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Procure\Application\Reporting\PR\Output\SaveAsExcel;
use Procure\Application\Reporting\PR\Output\SaveAsHTML;
use Procure\Application\Reporting\PR\Output\SaveAsOpenOffice;
use Procure\Application\Reporting\PR\Output\Header\HeadersSaveAsExcel;
use Procure\Application\Reporting\PR\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Reporting\PR\Output\Spreadsheet\OpenOfficeBuilder;
use Procure\Application\Service\Output\RowsSaveAsArray;
use Procure\Application\Service\Output\Contract\HeadersSaveAsSupportedType;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\NullRowFormatter;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeadersSaveAsArray;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\PrReportRepositoryInterface;

/**
 * PR Reporter
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

        if ($outputStrategy == HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $keyResult = \sprintf("result_%s", $filter->__toString());

        if ($this->getCache()->hasItem($keyResult)) {
            $results = $this->getCache()
                ->getItem($keyResult)
                ->get();
        } else {
            $results = $this->getReporterRespository()->getList($filter, $sort_by, $sort, $limit, $offset);
            $resultCache = $this->getCache()->getItem($keyResult);
            $resultCache->set($results);
            $this->getCache()->save($resultCache);
        }

        // var_dump($results);

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case HeadersSaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsArray();
                break;
            case HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsExcel($builder);
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

    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $outputStrategy = null)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($outputStrategy == HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL || $outputStrategy == HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getListWithCustomDTO($filter, $sort_by, $sort, $limit, $offset);

        $factory = null;
        $formatter = null;

        switch ($outputStrategy) {
            case HeadersSaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsArray();
                break;
            case HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new DefaultHeaderFormatter();
                $factory = new HeadersSaveAsExcel($builder);
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

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReporterRespository()->getAllRow($filter, $sort_by, $sort, $limit, $offset);

        // var_dump($results);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsArray();
                break;

            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new NullRowFormatter();
                $factory = new SaveAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_HMTL_TABLE:
                $formatter = new RowNumberFormatter();
                $factory = new SaveAsHTML();
                $factory->setOffset($offset);
                $factory->setLimit($limit);
                break;

            default:
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("total_rows_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getReporterRespository()->getAllRowTotal($filter);
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