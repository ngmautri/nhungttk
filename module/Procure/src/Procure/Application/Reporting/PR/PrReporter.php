<?php
namespace Procure\Application\Reporting\PR;

use Application\Application\Service\Document\Spreadsheet\DefaultExcelBuilder;
use Application\Application\Service\Document\Spreadsheet\DefaultOpenOfficeBuilder;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Application\Reporting\PR\Export\ExportAsExcel;
use Procure\Application\Reporting\PR\Export\ExportAsOpenOffice;
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
use Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;

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

    private $prGrReportRepository;

    /**
     *
     * @param ProcureAppSqlFilterInterface $filter
     * @param string $file_type
     * @param int $totalRecords
     * @return NULL|array|\Doctrine\Common\Collections\ArrayCollection|NULL|string
     */
    public function getPrGrReport(ProcureAppSqlFilterInterface $filter, $file_type, $totalRecords)
    {

        /**
         *
         * @var ArrayCollection $result
         */
        $result = $this->getPrGrReportRepository()->getList($filter);
        if ($result->isEmpty()) {
            return null;
        }

        $factory = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new DefaultExcelBuilder();
                $factory = new ExportAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new DefaultOpenOfficeBuilder();
                $factory = new ExportAsOpenOffice($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                return $result->toArray();

            default:
                return $result;
        }

        return $factory->execute($result);
    }

    /**
     *
     * @param SqlFilterInterface $filter
     * @return mixed
     */
    public function getPrGrReportTotal(ProcureAppSqlFilterInterface $filter)
    {
        $key = \sprintf("total_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getPrGrReportRepository()->getListTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }
        return $total;
    }

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

    public function getAllRow(SqlFilterInterface $filter, $file_type)
    {
        if (! $filter instanceof PrRowReportSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;

            $filter->setLimit($limit);
            $filter->setOffset($offset);
        }

        $results = $this->getReporterRespository()->getAllRow($filter);

        // var_dump($results);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new RowNumberFormatter();
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
                $formatter = new RowNumberFormatter();
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
     * @return \Procure\Infrastructure\Persistence\Reporting\PrReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Reporting\Contracts\PrGrReportInterface
     */
    public function getPrGrReportRepository()
    {
        return $this->prGrReportRepository;
    }

    /**
     *
     * @param PrGrReportInterface $prGrReportRepository
     */
    public function setPrGrReportRepository(PrGrReportInterface $prGrReportRepository)
    {
        $this->prGrReportRepository = $prGrReportRepository;
    }
}
