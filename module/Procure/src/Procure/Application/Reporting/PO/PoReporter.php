<?php
namespace Procure\Application\Reporting\PO;

use Application\Service\AbstractService;
use Procure\Application\Reporting\PO\Output\PoRowFormatter;
use Procure\Application\Reporting\PO\Output\SaveAsExcel;
use Procure\Application\Reporting\PO\Output\SaveAsHTML;
use Procure\Application\Reporting\PO\Output\SaveAsOpenOffice;
use Procure\Application\Reporting\PO\Output\Header\HeadersSaveAsExcel;
use Procure\Application\Reporting\PO\Output\Header\Spreadsheet\ExcelBuilder;
use Procure\Application\Service\Output\RowsSaveAsArray;
use Procure\Application\Service\Output\Contract\HeadersSaveAsSupportedType;
use Procure\Application\Service\Output\Contract\SaveAsSupportedType;
use Procure\Application\Service\Output\Formatter\DefaultRowFormatter;
use Procure\Application\Service\Output\Formatter\RowNumberFormatter;
use Procure\Application\Service\Output\Formatter\Header\DefaultHeaderFormatter;
use Procure\Application\Service\Output\Header\HeadersSaveAsArray;
use Procure\Application\Service\PO\Output\Spreadsheet\PoReportExcelBuilder;
use Procure\Application\Service\PO\Output\Spreadsheet\PoReportOpenOfficeBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Reporting\Contracts\PoApReportInterface;
use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PoApReportImpl;

/**
 * PO Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoReporter extends AbstractService
{

    /**
     *
     * @var PoReportRepositoryImpl $reportRespository;
     */
    private $reportRespository;

    private $poApReportRepository;

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == HeadersSaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == HeadersSaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReportRespository()->getList($filter, $sort_by, $sort, $limit, $offset);

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
            $total = $this->getReportRespository()->getListTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }
        return $total;
    }

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type, $totalRecords)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }

        $results = $this->getReportRespository()->getAllRow($filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new PoRowFormatter(new DefaultRowFormatter());
                $factory = new RowsSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new PoReportExcelBuilder();
                $formatter = new PoRowFormatter(new RowNumberFormatter());
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new PoReportOpenOfficeBuilder();
                $formatter = new PoRowFormatter(new RowNumberFormatter());
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
                $formatter = new PoRowFormatter(new DefaultRowFormatter());
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
            $total = $this->getReportRespository()->getAllRowTotal($filter);
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
     * @param SqlFilterInterface $filter
     * @param string $file_type
     * @param int $totalRecords
     */
    public function getPoApReport(ProcureAppSqlFilterInterface $filter, $file_type, $totalRecords)
    {
        $rep = new PoApReportImpl($this->getDoctrineEM());
        $result = $rep->getList($filter);

        return $result;
    }

    /**
     *
     * @param SqlFilterInterface $filter
     * @return mixed
     */
    public function getPoApReportTotal(ProcureAppSqlFilterInterface $filter)
    {
        $key = \sprintf("total_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getPoApReportRepository()->getListTotal($filter);
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
     * @return \Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl
     */
    public function getReportRespository()
    {
        return $this->reportRespository;
    }

    /**
     *
     * @param PoReportRepositoryImpl $reportRespository
     */
    public function setReportRespository(PoReportRepositoryImpl $reportRespository)
    {
        $this->reportRespository = $reportRespository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\Reporting\Contracts\PoApReportInterface
     */
    public function getPoApReportRepository()
    {
        return $this->poApReportRepository;
    }

    /**
     *
     * @param PoApReportInterface $poApReportRepository
     */
    public function setPoApReportRepository(PoApReportInterface $poApReportRepository)
    {
        $this->poApReportRepository = $poApReportRepository;
    }
}
