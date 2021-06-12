<?php
namespace InventoryTes\Transaction;

use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Reporting\Transaction\TrxReporter;
use Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class TrxReporterTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var TrxReporter $sv ; */
            $sv = Bootstrap::getServiceManager()->get(TrxReporter::class);

            // $filter = new TrxReportSqlFilter();
            $filter = new TrxRowReportSqlFilter();
            $filter->setIsActive(1);
            $filter->setDocMonth(null);
            $filter->setDocYear(2020);
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;
            $file_type = SaveAsSupportedType::OUTPUT_IN_ARRAY;

            $rootEntity = $sv->getAllRow($filter, $sort_by, $sort, $limit, $offset, $file_type);
            var_dump(count($rootEntity));
        } catch (InvalidArgumentException $e) {
            echo ($e->getMessage());
        }
    }
}