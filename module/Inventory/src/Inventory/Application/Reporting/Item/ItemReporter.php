<?php
namespace Inventory\Application\Reporting\Item;

use Application\Service\AbstractService;
use Inventory\Application\Export\Item\SaveAsArray;
use Inventory\Application\Export\Item\Contracts\SaveAsSupportedType;
use Inventory\Application\Export\Item\Formatter\InOutOnHandFormatter;
use Inventory\Application\Export\Item\Formatter\NullFormatter;
use Inventory\Application\Reporting\Item\Export\InOutOnHandSaveAsArray;
use Inventory\Application\Reporting\Item\Export\InOutOnhandSaveAsExcel;
use Inventory\Application\Reporting\Item\Export\InOutOnhandSaveAsOpenOffice;
use Inventory\Application\Reporting\Item\Export\SaveAsExcel;
use Inventory\Application\Reporting\Item\Export\SaveAsOpenOffice;
use Inventory\Application\Reporting\Item\Export\Spreadsheet\ExcelBuilder;
use Inventory\Application\Reporting\Item\Export\Spreadsheet\InOutOnhandExcelBuilder;
use Inventory\Application\Reporting\Item\Export\Spreadsheet\InOutOnhandOpenOfficeBuilder;
use Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Doctrine\ItemTrxReportRepositoryImpl;

/**
 * Item Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReporter extends AbstractService
{

    private $reporterRespository;

    public function getList($filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $results = $this->getReporterRespository()->getItemList($filter, $sort_by, $sort, $limit, $offset);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new NullFormatter();
                $factory = new SaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new NullFormatter();
                $factory = new SaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

                $builder = new ExcelBuilder();
                $formatter = new NullFormatter();
                $factory = new SaveAsOpenOffice($builder);
                break;

            default:
                $formatter = new NullFormatter();
                $factory = new SaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    public function getInOutOnhand($filter, $sort_by, $sort, $limit, $offset, $file_type)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $rep = new ItemTrxReportRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getInOutOnhand($filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return null;
        }

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SaveAsSupportedType::OUTPUT_IN_ARRAY:
                $formatter = new InOutOnHandFormatter();
                $factory = new InOutOnHandSaveAsArray();
                break;
            case SaveAsSupportedType::OUTPUT_IN_EXCEL:

                $builder = new InOutOnhandExcelBuilder();
                $formatter = new NullFormatter();
                $factory = new InOutOnhandSaveAsExcel($builder);
                break;

            case SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE:

                $builder = new InOutOnhandOpenOfficeBuilder();
                $formatter = new NullFormatter();
                $factory = new InOutOnhandSaveAsOpenOffice($builder);
                break;

            default:
                $formatter = new InOutOnHandFormatter();
                $factory = new InOutOnHandSaveAsArray();
                break;
        }

        return $factory->saveAs($results, $formatter);
    }

    /**
     *
     * @param SqlFilterInterface $filter
     * @return number|mixed
     */
    public function getInOutOnhandTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("_item_in_on_onhand_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $rep = new ItemTrxReportRepositoryImpl($this->getDoctrineEM());
            $total = $rep->getInOutOnhandTotal($filter);
            $resultCache->set($total);
            $this->getCache()->save($resultCache);
        } else {
            $total = $this->getCache()
                ->getItem($key)
                ->get();
        }

        return $total;
    }

    public function getListTotal(SqlFilterInterface $filter)
    {
        $key = \sprintf("_item_list_%s", $filter->__toString());

        $resultCache = $this->getCache()->getItem($key);
        if (! $resultCache->isHit()) {
            $total = $this->getReporterRespository()->getItemListTotal($filter);
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
     * @return \Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }

    /**
     *
     * @param ItemReportRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(ItemReportRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }
}
