<?php
namespace Inventory\Application\Reporting\Transaction;

use Application\Service\AbstractService;
use Inventory\Application\Export\Transaction\RawRowsSaveAsArray;
use Inventory\Application\Export\Transaction\RowsSaveAsArray;
use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Export\Transaction\Formatter\NullRowFormatter;
use Inventory\Application\Export\Transaction\Formatter\TrxRowFormatter;
use Inventory\Application\Reporting\Transaction\Export\RowsSaveAsExcel;
use Inventory\Application\Reporting\Transaction\Export\RowsSaveAsOpenOffice;
use Inventory\Application\Reporting\Transaction\Export\Spreadsheet\ExcelBuilder;
use Inventory\Application\Reporting\Transaction\Export\Spreadsheet\OpenOfficeBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;

/**
 * Item Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class StockReporter extends AbstractService
{

    private $reporterRespository;

    /**
     *
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @param int $file_type
     * @throws \InvalidArgumentException
     * @return NULL|NULL[]
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $results = $this->getReporterRespository()->getList($filter, $sort_by, $sort, $limit, $offset);
        var_dump($results);
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
                $factory = new RowsSaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

                $builder = new ExcelBuilder();
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsOpenOffice($builder);
                break;

            default:
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsArray();
                break;
        }

        $factory->setLogger($this->getLogger());

        return $factory->saveAs($results, $formatter);
    }

    /**
     *
     * @param SqlFilterInterface $filter
     * @return number|mixed
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("_trx_report_%s", $filter->__toString());

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

    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset, $file_type, $total_records = null)
    {
        $results = $this->getReporterRespository()->getAllRow($filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new TrxRowFormatter(new NullRowFormatter());
                $factory = new RawRowsSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:
                $builder = new ExcelBuilder();
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:
                $builder = new OpenOfficeBuilder();
                $formatter = new NullRowFormatter();
                $factory = new RowsSaveAsOpenOffice($builder);
                break;
            default:
                $formatter = new TrxRowFormatter(new NullRowFormatter());
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

    // ==========================

    /**
     *
     * @return \Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /*
     *
     */
    public function setReporterRespository(TrxReportRepositoryImpl $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }
}
