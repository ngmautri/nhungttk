<?php
namespace Inventory\Application\Reporting\Item;

use Application\Service\AbstractService;
use Inventory\Application\Export\Item\SaveAsArray;
use Inventory\Application\Export\Item\Contracts\SaveAsSupportedType;
use Inventory\Application\Export\Item\Contracts\SupportedExportType;
use Inventory\Application\Export\Item\Formatter\NullFormatter;
use Inventory\Application\Reporting\ItemSerial\Export\Formatter\DefaultItemSerialFormatter;
use Inventory\Application\Reporting\Item\Export\SaveAsExcel;
use Inventory\Application\Reporting\Item\Export\SaveAsOpenOffice;
use Inventory\Application\Reporting\Item\Export\Spreadsheet\ExcelBuilder;
use Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\Filter\ItemSerialSqlFilter;

/**
 * Item Serial Report Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialReporter extends AbstractService
{

    private $reporterRespository;

    public function getList($filter, $file_type)
    {
        if (! $filter instanceof ItemSerialSqlFilter) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        if ($file_type == SaveAsSupportedType::OUTPUT_IN_EXCEL || $file_type == SaveAsSupportedType::OUTPUT_IN_OPEN_OFFICE) {
            $limit = null;
            $offset = null;
        }
        $results = $this->getReporterRespository()->getList($filter);

        $factory = null;
        $formatter = null;

        switch ($file_type) {
            case SupportedExportType::ARRAY:
                $formatter = new DefaultItemSerialFormatter();
                $factory = new SaveAsArray();
                break;
            case SupportedExportType::EXCEL:

                $builder = new ExcelBuilder();
                $formatter = new NullFormatter();
                $factory = new SaveAsExcel($builder);
                break;

            case SupportedExportType::OPEN_OFFICE:

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

    public function getListTotal(ItemSerialSqlFilter $filter)
    {
        $key = \sprintf("_item_serial_list_%s", $filter->__toString());

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

    /*
     * |=============================
     * |Getter and Setter
     * |
     * |=============================
     */

    /**
     *
     * @param ItemSerialQueryRepositoryInterface $reporterRespository
     */
    public function setReporterRespository(ItemSerialQueryRepositoryInterface $reporterRespository)
    {
        $this->reporterRespository = $reporterRespository;
    }

    /**
     *
     * @return \Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface
     */
    public function getReporterRespository()
    {
        return $this->reporterRespository;
    }
}
